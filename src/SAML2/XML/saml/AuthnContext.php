<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use DOMElement;
use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\saml\AuthenticatingAuthority;
use SAML2\XML\saml\AuthnContextClassRef;
use SAML2\XML\saml\AuthnContextDeclRef;
use SAML2\XML\saml\AuthnContextRef;
use Webmozart\Assert\Assert;

/**
 * Class representing SAML2 AuthnContext
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package SimpleSAMLphp
 */
class AuthnContext extends \SAML2\XML\AbstractConvertable
{
    /** @var \SAML2\XML\saml\AuthnContextClassRef|null */
    private $authnContextClassRef = null;

    /** @var \SAML2\XML\saml\AuthnContextDeclRef|null */
    private $authnContextDeclRef = null;

    /** @var \SAML2\XML\saml\AuthnContextDecl|null */
    private $authnContextDecl = null;

    /** @var \SAML2\XML\saml\AuthenticatingAuthority[]|null */
    private $authenticatingAuthorities = null;


    /**
     * Initialize an AuthnContext.
     *
     * @param \SAML2\XML\saml\AuthnContextClassRef|null $authnContextClassRef
     * @param \SAML2\XML\saml\AuthnContextDecl|null $authnContextDecl
     * @param \SAML2\XML\saml\AuthnContextDeclRef|null $authnContextDeclRef
     * @param \SAML2\XML\saml\AuthenticatingAuthority[]|null $authenticatingAuthorities
     */
    public function __construct(
        ?AuthnContextClassRef $authnContextClassRef,
        ?AuthnContextDecl $AuthnContextDecl,
        ?AuthnContextDeclRef $authnContextDeclRef,
        ?array $authenticatingAuthorities
    ) {
        if (!is_null($authnContextClassRef)) {
            Assert::oneOf(
                null,
                [$authnContextDecl, $authnContextDeclRef],
                'Can only have one of AuthnContextDecl/AuthnContextDeclRef'
            );
        } else {
            Assert::false(
                is_null($authnContextDecl) && is_null($authnContextDeclRef),
                'You need either an AuthnContextDecl or an AuthnContextDeclRef'
            );
        }

        $this->setAuthnContextClassRef($authnContextClassRef);

        if (!is_null($authnContextDecl)) {
            $this->setAuthnContextDecl($authnContextDecl);
        }

        if (!is_null($authnContextDeclRef)) {
            $this->setAuthnContextDeclRef($authnContextDeclRef);
        }

        $this->setAuthenticatingAuthorities($authenticatingAuthorities);       
    }


    /**
     * Collect the value of the authnContextClassRef-property
     *
     * @return \SAML2\XML\saml\AuthnContextClassRef|null
     */
    public function getAuthnContextClassRef(): ?AuthnContextClassRef
    {
        return $this->authnContextClassRef;
    }


    /**
     * Set the value of the authnContextClassRef-property
     *
     * @param \SAML2\XML\saml\AuthnContextClassRef|null $authnContextClassRef
     * @return void
     */
    public function setAuthnContextClassRef(?AuthnContextClassRef $authnContextClassRef): void
    {
        $this->authnContextClassRef = $authnContextClassRef;
    }


    /**
     * Collect the value of the authnContextDeclRef-property
     *
     * @return \SAML2\XML\saml\AuthnContextDeclRef|null
     */
    public function getAuthnContextDeclRef(): ?AuthnContextDeclRef
    {
        return $this->authnContextDeclRef;
    }


    /**
     * Set the value of the authnContextDeclRef-property
     *
     * @param \SAML2\XML\saml\AuthnContextDeclRef|null $authnContextDeclRef
     * @return void
     */
    public function setAuthnContextDeclRef(?AuthnContextDeclRef $authnContextDeclRef): void
    {
        $this->authnContextDeclRef = $authnContextDeclRef;
    }


    /**
     * Collect the value of the authnContextDecl-property
     *
     * @return \SAML2\XML\saml\AuthnContextDecl|null
     */
    public function getAuthnContextDecl(): ?AuthnContextDecl
    {
        return $this->authnContextDecl;
    }


    /**
     * Set the value of the authnContextDecl-property
     *
     * @param \SAML2\XML\saml\AuthnContextDecl|null $authnContextDecl
     * @return void
     */
    public function setAuthnContextDecl(?AuthnContextDecl $authnContextDecl): void
    {
        $this->authnContextDecl = $authnContextDecl;
    }


    /**
     * Collect the value of the authenticatingAuthorities-property
     *
     * @return \SAML2\XML\saml\AuthenticatingAuthority[]
     */
    public function getAuthticatingAuthorities(): array
    {
        return $this->authenticatingAuthorities;
    }


    /**
     * Set the value of the authenticatingAuthorities-property
     *
     * @param \SAML2\XML\saml\AuthenticatingAuthority[] $authenticatingAuthorities
     * @return void
     */
    public function setAuthenticatingAuthorities(array $authenticatingAuthorities): void
    {
        $this->authenticatingAuthorities = $authenticatingAuthorities;
    }


    /**
     * Convert XML into a AuthnContext
     *
     * @param \DOMElement|null $xml The XML element we should load
     * @return self|null
     */
    public static function fromXML(?DOMElement $xml): ?object
    {
        if ($xml === null) {
            return null;
        }

        /** @var \DOMElement[] $authnContextClassRef */
        $authnContextClassRef = Utils::xpQuery($xml, './saml_assertion:AuthnContextClassRef');
        Assert::maxCount($authnContextClassRef, 1);

        /** @var \DOMElement[] $authnContextDeclRef */
        $authnContextDeclRef = Utils::xpQuery($xml, './saml_assertion:AuthnContextDeclRef');
        Assert::maxCount($authnContextDeclRef, 1);

        /** @var \DOMElement[] $authnContextDecl */
        $authnContextDecl = Utils::xpQuery($xml, './saml_assertion:AuthnContextDecl');
        Assert::maxCount($authnContextDecl, 1);

        if (!empty($authnContextClassRef)) {
            Assert::oneOf(
                [],
                [$authnContextDecl, $authnContextDeclRef],
                'Can only have one of AuthnContextDecl/AuthnContextDeclRef'
            );
        } else {
            Assert::false(
                empty($authnContextDecl) && empty($authnContextDeclRef),
                'You need either an AuthnContextDecl or an AuthnContextDeclRef'
            );
        }

        /** @var \DOMElement[] $authenticatingAuthorities */
        $authenticatingAuthorities = Utils::xpQuery($xml, './saml_assertion:AuthenticatingAuthority');

        return new self(
            AuthnContextClassRef::fromXML($authnContextClassRef[0]),
            AuthnContextDecl::fromXML(empty($authnContextDecl) ? null : $authnContextDecl[0]),
            AuthnContextDeclRef::fromXML(empty($authnContextDeclRef) ? null : $authnContextDeclRef[0]),
            array_map([$this, 'fromXML'], $authenticatingAuthorities) ?: null
        );
    }


    /**
     * Convert this AuthContextDeclRef to XML.
     *
     * @param \DOMElement|null $parent The element we should append this AuthnContextDeclRef to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_SAML, 'saml:AuthnContext');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:AuthnContext');
            $parent->appendChild($e);
        }

        if (!empty($this->authnContextClassRef)) {
            $this->authnContextClassRef->toXML($e);

            Assert::oneOf(
                null,
                [$this->authnContextDecl, $this->authnContextDeclRef],
                'Can only have one of AuthnContextDecl/AuthnContextDeclRef'
            );
        } else {
            Assert::false(
                empty($this->authnContextDecl) && empty($this->authnContextDeclRef),
                'You need either an AuthnContextDecl or an AuthnContextDeclRef'
            );
        }

        if (!empty($this->authnContextDecl)) {
            $this->authnContextDecl->toXML($e);
        }

        if (!empty($this->authnContextDeclRef)) {
            $this->authnContextDeclRef->toXML($e);
        }

        foreach ($this->authenticatingAuthorities as $authority) {
            $authority->toXML($e);
        }

        return $e;
    }
}
