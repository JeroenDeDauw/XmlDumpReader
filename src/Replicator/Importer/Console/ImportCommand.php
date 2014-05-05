<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Replicator\Importer\ImportStats;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\Importer\StatsTrackingReporter;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import' );
		$this->setDescription( 'Imports entities from an XML dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the XML dump'
		);
	}

	/**
	 * @var ServiceFactory|null
	 */
	private $factory = null;

	public function setServiceFactory( ServiceFactory $factory ) {
		$this->factory = $factory;
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->initServiceFactory( $output );

		$pagesImporter = new PagesImporter(
			$this->newImporter( $this->newReporter( $output ) ),
			new ConsoleStatsReporter( $output )
		);

		$pagesImporter->importPages( $this->getDumpIterator( $input ) );
	}

	private function initServiceFactory( OutputInterface $output ) {
		if ( $this->factory === null ) {
			try {
				$this->factory = ServiceFactory::newFromConfig();
			}
			catch ( RuntimeException $ex ) {
				$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
				$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
				return;
			}
		}
	}

	private function getDumpIterator( InputInterface $input ) {
		return $this->newDumpReader( $input->getArgument( 'file' ) )->getIterator();
	}

	private function newDumpReader( $file ) {
		$dumpReaderFactory = new ReaderFactory();
		return $dumpReaderFactory->newDumpReaderForFile( $file );
	}

	private function newImporter( PageImportReporter $reporter ) {
		return new PageImporter(
			$this->factory->newDumpStore(),
			$this->factory->newEntityDeserializer(),
			$this->factory->newQueryStoreWriter(),
			$reporter
		);
	}

	private function newReporter( OutputInterface $output ) {
		return $output->isVerbose() ? new VerboseReporter( $output ) : new SimpleReporter( $output );
	}


}
