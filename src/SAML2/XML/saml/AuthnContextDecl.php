<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\Utils;
use Webmozart\Assert\Assert;

/**
 * Class representing SAML2 AuthnContextDecl
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package SimpleSAMLphp
 */
class AuthnContextDecl extends \SAML2\XML\AbstractConvertable
{
    /** @var \DOMElement|null */
    private $decl;


    /**
     * Initialize an AuthnContextDecl.
     *
     * @param \DOMElement $decl
     */
    public function __construct(DOMElement $decl)
    {
        $this->setDecl($decl);
    }


    /**
     * Collect the value of the decl-property
     *
     * @return \DOMElement
     *
     * @throws \InvalidArgumentException if assertions are false
     */
    public function getDecl(): DOMElement
    {
        Assert::notNull($this->decl);

        return $this->decl;
    }


    /**
     * Set the value of the decl-property
     *
     * @param \DOMElement $name
     * @return void
     */
    public function setDecl(DOMElement $decl): void
    {
        $this->decl = $decl;
    }


    /**
     * Convert XML into a AuthnContextDecl
     *
     * @param \DOMElement|null $xml The XML element we should load
     * @return self
     */
    public static function fromXML(DOMElement $xml = null): ?object
    {
        if ($xml === null) {
            return null;
        }

        return new self($xml);
    }


    /**
     * Convert this AuthContextDecl to XML.
     *
     * @param \DOMElement|null $parent The element we should append this AuthnContextDecl to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        Assert::notNull($this->decl, 'Cannot convert AuthnContextDecl to XML without a Decl set');

        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_SAML, 'saml:AuthnContextDecl');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:AuthnContextDecl');
            $parent->appendChild($e);
        }
        $e->appendChild($e->ownerDocument->importNode($this->decl, true));
        return $e;
    }
}
