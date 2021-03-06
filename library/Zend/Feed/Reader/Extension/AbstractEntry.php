<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader\Extension;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Zend\Feed\Reader;

/**
* @category Zend
* @package Reader\Reader
*/
abstract class AbstractEntry
{
    /**
     * Feed entry data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * DOM document object
     *
     * @var DOMDocument
     */
    protected $_domDocument = null;

    /**
     * Entry instance
     *
     * @var Zend_Feed_Entry_Abstract
     */
    protected $_entry = null;

    /**
     * Pointer to the current entry
     *
     * @var int
     */
    protected $_entryKey = 0;

    /**
     * XPath object
     *
     * @var DOMXPath
     */
    protected $_xpath = null;

    /**
     * XPath query
     *
     * @var string
     */
    protected $_xpathPrefix = '';

    /**
     * Constructor
     *
     * @param  Zend_Feed_Entry_Abstract $entry
     * @param  int $entryKey
     * @param  string $type
     * @return void
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        $this->_entry       = $entry;
        $this->_entryKey    = $entryKey;
        $this->_domDocument = $entry->ownerDocument;

        if ($type !== null) {
            $this->_data['type'] = $type;
        } else {
            $this->_data['type'] = Reader\Reader::detectType($entry->ownerDocument, true);
        }
        // set the XPath query prefix for the entry being queried
        if ($this->getType() == Reader\Reader::TYPE_RSS_10
            || $this->getType() == Reader\Reader::TYPE_RSS_090
        ) {
            $this->setXpathPrefix('//rss:item[' . ($this->_entryKey+1) . ']');
        } elseif ($this->getType() == Reader\Reader::TYPE_ATOM_10
                  || $this->getType() == Reader\Reader::TYPE_ATOM_03
        ) {
            $this->setXpathPrefix('//atom:entry[' . ($this->_entryKey+1) . ']');
        } else {
            $this->setXpathPrefix('//item[' . ($this->_entryKey+1) . ']');
        }
    }

    /**
     * Get the DOM
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->_domDocument;
    }

    /**
     * Get the Entry's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $assumed = $this->getDomDocument()->encoding;
        return $assumed;
    }

    /**
     * Get the entry type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_data['type'];
    }

    /**
     * Set the XPath query
     *
     * @param  DOMXPath $xpath
     * @return Reader\Reader_Extension_EntryAbstract
     */
    public function setXpath(DOMXPath $xpath)
    {
        $this->_xpath = $xpath;
        $this->_registerNamespaces();
        return $this;
    }

    /**
     * Get the XPath query object
     *
     * @return DOMXPath
     */
    public function getXpath()
    {
        if (!$this->_xpath) {
            $this->setXpath(new DOMXPath($this->getDomDocument()));
        }
        return $this->_xpath;
    }

    /**
     * Serialize the entry to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Get the XPath prefix
     *
     * @return string
     */
    public function getXpathPrefix()
    {
        return $this->_xpathPrefix;
    }

    /**
     * Set the XPath prefix
     *
     * @param  string $prefix
     * @return Reader\Reader_Extension_EntryAbstract
     */
    public function setXpathPrefix($prefix)
    {
        $this->_xpathPrefix = $prefix;
        return $this;
    }

    /**
     * Register XML namespaces
     *
     * @return void
     */
    abstract protected function _registerNamespaces();
}
