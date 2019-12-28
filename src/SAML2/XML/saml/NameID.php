<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;
use SAML2\Constants;

/**
 * Class representing the saml:NameID element.
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */
class NameID extends NameIDType
{
    /**
     * Set the name of this XML element to "saml:NameID"
     *
     * @var string
     */
    protected $nodeName = 'saml:NameID';


    /**
     * Convert XML into a NameID
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        $Format = $xml->hasAttribute('Format') ? $xml->getAttribute('Format') : Constants::NAMEID_ENTITY;
        $SPProvidedID = $xml->hasAttribute('SPProvidedID') ? $xml->getAttribute('SPProvidedID') : null;
        $value = $xml->textContent;

        $NameQualifier = $xml->hasAttribute('NameQualifier')
            ? $xml->getAttribute('NameQualifier')
            : null;

        $SPNameQualifier = $xml->hasAttribute('SPNameQualifier')
            ? $xml->getAttribute('SPNameQualifier')
            : null;

        return new self($Format, $SPProvidedID, $NameQualifier, $SPNameQualifier, $value);
    }
}
