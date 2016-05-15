<?php
namespace Resources\Action;

use Silex\Application;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scrape action class specific to sainsburys product
 *
 * @package Resources
 * @subpackage Action
 * @author Shane Exley <shaneexley@live.co.uk>
 */
class Scrape
{
    const PRODUCT_PRICE_DEFAULT = '0.00';

    /**
     * @var string URL
     */
    private $source;

    /**
     * @param String $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Scrape the URL for required data
     *
     * @todo Product Description
     * @return JSON
     */
    public function getScrapedData()
    {
        if (is_null($this->source)) {
            throw new \Exception('No URL parsed');
        }

        // New Goutte client
        $client  = new \Goutte\Client();
        $crawler = $client->request('GET', $this->source);
        $total   = 0;
        $results = array();

        $status_code = $client->getResponse()->getStatus();

        if (200 === $status_code) {
            $nodes                  = $crawler->filter('.product');
            $results['results'][]   = $nodes->each(function($node) use (&$total, $client) {

                $product['title']       = $this->getProductTitle($node);
                $product['size']        = $this->getProductSize($node, $client);
                $product['unit_price']  = $this->getProductUnitPrice($node);
                $product['description'] = $this->getProductDescription($node, $client);
                $total += $product['unit_price'];

                return $product;
            });

            $results['total'] = number_format($total, 2);

        }

        return json_encode(
            $results, 
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Get Product Title
     *
     * @param Symfony\Component\DomCrawler\Crawler $crawler
     * @return string
     */
    private function getProductTitle(Crawler $crawler)
    {
        $node = $crawler->filter('.productInner > .productInfoWrapper > .productInfo > h3 > a');

        return trim($node->text());
    }

    /**
     * Get Product Unit Price
     *
     * @param Symfony\Component\DomCrawler\Crawler $crawler
     * @return string
     */
    private function getProductUnitPrice(Crawler $crawler)
    {
        $node = $crawler->filter('.productInner > .addToTrolleytabBox > .addToTrolleytabContainer > .pricingAndTrolleyOptions > .priceTab .pricing > .pricePerUnit');

        if (preg_match( '/[0-9]{0,}[.][0-9]{0,}/', $node->text(), $price)) {
            return $price[0];
        }

        return self::PRODUCT_PRICE_DEFAULT;
    }

    /**
     * Get Product Page HTML Size
     *
     * @param Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Goutte\Client $client
     * @return string
     */
    private function getProductSize(Crawler $crawler, \Goutte\Client $client)
    {
        $size     = 0;
        $tmp_path = '/tmp/sainsbury_product.html';
        $product  = $this->scanProductLink($crawler, $client)->html();

        file_put_contents($tmp_path, $product);
        $size = filesize($tmp_path);
        $size = round(($size / 1024), 2) . 'kb';

        if (file_exists($tmp_path)) {
            unlink($tmp_path);
        }

        return $size;
    }

    /**
     * Get Product Description
     *
     * @param Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Goutte\Client $client
     * @return string
     */
    private function getProductDescription(Crawler $crawler, \Goutte\Client $client)
    {
        $product = $this->scanProductLink($crawler, $client);

        return $product->filter('#information > productcontent > htmlcontent> div.productText > p')->eq(0)->html();
    }

    /**
     * Get Product Link Scan
     *
     * @param Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Goutte\Client $client
     * @return Symfony\Component\DomCrawler\Crawler
     */
    private function scanProductLink(Crawler $crawler, \Goutte\Client $client)
    {
        $node = $crawler->filter('.productInner > .productInfoWrapper > .productInfo > h3 > a');
        $link = $node->selectLink(trim($node->text()))->link();

        return $client->request('GET', $link->getUri());
    }
}