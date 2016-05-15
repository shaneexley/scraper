<?php

namespace Resources\Console;

use Resources\Action\Scrape as ScrapeAction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scrape console request handler
 *
 * @package Resources
 * @subpackage Console
 * @author Shane Exley <shaneexley@live.co.uk>
 */
class Scrape extends Command
{
    /**
     * Configure command arguments
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('scrape')
             ->addArgument(
                'source',
                InputArgument::REQUIRED
            )
            ->setDescription("Scraping...");
    }

    /**
     * Execute the scraper
     *
     * @param   InputInterface    $input
     * @param   OutputInterface   $output
     * @return  void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result  = null;
        $scraper = new ScrapeAction();
        $scraper->setSource($input->getArgument('source'));

        try {
            $result = $scraper->getScrapedData();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

        $output->writeln($result);
    }
}