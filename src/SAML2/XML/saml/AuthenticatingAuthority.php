<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;
use SAML2\Constants;
use SAML2\Utils;
use Webmozart\Assert\Assert;

/**
 * Class representing SAML2 AuthenticatingAuthority
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package SimpleSAMLphp
 */
class AuthenticatingAuthority extends \SAML2\XML\AbstractConvertable
{
    /** @var string */
    private $authority;


    /**
     * Initialize an AuthicatingAuthority.
     *
     * @param string $authority
     */
    public function __construct(string $authority)
    {
        $this->setAuthority = $authority;
    }


    /**
     * Collect the value of the authority-property
     *
     * @return string
     *
     * @throws \InvalidArgumentException if assertions are false
     */
    public function getAuthority(): string
    {
        Assert::stringNotEmpty($this->authority);
        return $this->authority;
    }


    /**
     * Set the value of the authority-property
     *
     * @param string $name
     * @return void
     */
    public function setAuthority(string $authority): void
    {
        $authority = trim($authority);
        Assert::stringNotEmpty($authority);
        $this->authority = $authority;
    }


    /**
     * Convert XML into a AuthenticatingAuthority
     *
     * @param \DOMElement|null $xml The XML element we should load
     * @return self|null
     */
    public static function fromXML(DOMElement $xml = null): ?object
    {
        if ($xml === null) {
            return null;
        }

        return new self($xml->textContent);
    }


    /**
     * Convert this AuthenticatingAuthority to XML.
     *
     * @param \DOMElement|null $parent The element we should append this AuthnContextClassRef to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        Assert::stringNotEmpty($this->authority, 'Cannot convert AuthenticatingAuthority to XML without an authority set');

        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_SAML, 'saml:AuthenticatingAuthority');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:AuthenticatingAuthority');
            $parent->appendChild($e);
        }

        $e->textContent = $this->authority;

        return $e;
    }
}
