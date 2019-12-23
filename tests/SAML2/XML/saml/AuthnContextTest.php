<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\Utils;

/**
 * Class \SAML2\XML\saml\AuthnContextTest
 */
class AuthnContextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testMarshallingWithClassRef(): void
    {
        $authnContext = new AuthnContext(
            new AuthnContextClassRef(Constants::AC_PASSWORD_PROTECTED_TRANSPORT),
            null,
            new AuthnContextDeclRef('/relative/path/to/document.xml'),
            new AuthenticatingAuthority('https://sp.example.com/SAML2')
        );

        $document = DOMDocumentFactory::fromString('<root />');
        /** @var \DOMElement $document->firstChild */
        $authnContextElement = $authnContext->toXML($document->firstChild);

        $authnContextElements = Utils::xpQuery(
            $authnContextElement,
            '/root/saml_assertion:AuthnContext'
        );
        $this->assertCount(1, $authnContextElements);

        $authnContextClassRefElements = Utils::xpQuery(
            $authnContextElements[0],
            './saml_assertion:AuthnContextClassRef'
        );

        $this->assertCount(1, $authnContextClassRefElements);
        $authnContextClassRefElement = $authnContextClassRefElements[0];

        $this->assertEquals(Constants::AC_PASSWORD_PROTECTED_TRANSPORT, $authnContextClassRefElement->textContent);

        $authnContextDeclRefElements = Utils::xpQuery(
            $authnContextElements[0],
            './saml_assertion:AuthnContextDeclRef'
        );

        $this->assertCount(1, $authnContextDeclRefElements);
        $authnContextDeclRefElement = $authnContextDeclRefElements[0];

        $this->assertEquals('/relative/path/to/document.xml', $authnContextDeclRefElement->textContent);

        $authenticatingAuthorityElements = Utils::xpQuery(
            $authnContextElements[0],
            './saml_assertion:AuthenticatingAuthority'
        );

        $this->assertCount(1, $authenticatingAuthorityElements);
        $authenticatingAuthorityElement = $authenticatingAuthorityElements[0];

        $this->assertEquals('https://sp.example.com/SAML2', $authenticatingAuthorityElement->textContent);
    }


    /**
     * @return void
     */
    public function testMarshallingWithoutClassRef(): void
    {
        $document = DOMDocumentFactory::fromString(<<<XML
<samlacpass:AuthenticationContextDeclaration>
  <samlacpass:Identification nym="verinymity">
    <samlacpass:Extension>
       <safeac:NoVerification/>
    </samlacpass:Extension>
  </samlacpass:Identification>
</samlacpass:AuthenticationContextDeclaration>
XML
        );

        /** @var \DOMElement $document->firstChild */
        $authnContextDecl = new AuthnContextDecl($document->firstChild);
        $authenticatingAuthority = new AuthenticatingAuthority('https://sp.example.com/SAML2');

        $authnContext = new AuthnContext(
            null,
            $authnContextDecl,
            null,
            [$authenticatingAuthority]
        );

        $document = DOMDocumentFactory::fromString('<root />');
        /** @var \DOMElement $document->firstChild */
        $authnContextElement = $authnContext->toXML($document->firstChild);

        $authnContextElements = Utils::xpQuery(
            $authnContextElement,
            '/root/saml_assertion:AuthnContext'
        );
        $this->assertCount(1, $authnContextElements);

        $authnContextDeclElements = Utils::xpQuery(
            $authnContextElements[0],
            './saml_assertion:AuthnContextDecl'
        );

        $this->assertCount(1, $authnContextDeclElements);
        $authnContextDeclElement = $authnContextDeclElements[0];

        $this->assertEquals('samlacpass:AuthenticationContextDeclaration', $authnContextDeclElement->firstChild->tagName);

        $authenticatingAuthorityElements = Utils::xpQuery(
            $authnContextElements[0],
            './saml_assertion:AuthenticatingAuthority'
        );

        $this->assertCount(1, $authenticatingAuthorityElements);
        $authenticatingAuthorityElement = $authenticatingAuthorityElements[0];

        $this->assertEquals('https://sp.example.com/SAML2', $authenticatingAuthorityElement->textContent);
    }


    /**
     * @return void
     */
    public function testUnmarshallingWithClassRef(): void
    {
        $samlNamespace = Constants::NS_SAML;

        $document = DOMDocumentFactory::fromString(<<<XML
<saml:AuthnContext xmlns:saml="{$samlNamespace}">
  <saml:AuthnContextClassRef>
    urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport
  </saml:AuthnContextClassRef>
  <saml:AuthnContextDeclRef>
    /relative/path/to/document.xml
  </saml:AuthnContextDeclRef>
  <saml:AuthenticatingAuthority>
    https://sp.example.com/SAML2
  </saml:AuthenticatingAuthority>
</saml:AuthnContext>
XML
        );

        /** @var \DOMElement $document->firstChild */
        $authnContext = AuthnContext::fromXML($document->firstChild);

        $this->assertEquals(Constants::AC_PASSWORD_PROTECTED_TRANSPORT, $authnContext->getAuthnContextClassRef()->getClassRef());

        $this->assertEquals('/relative/path/to/document.xml', $authnContext->getAuthnContextDeclRef()->getDeclRef());

        $this->assertEquals('https://sp.example.com/SAML2', $authnContext->getAuthticatingAuthorities()[0]->getAuthority());
    }


    /**
     * @return void
     */
    public function testUnmarshallingWithoutClassRef(): void
    {
        $samlNamespace = Constants::NS_SAML;

        $document = DOMDocumentFactory::fromString(<<<XML
<saml:AuthnContext xmlns:saml="{$samlNamespace}">
  <saml:AuthnContextDecl>
    <samlacpass:AuthenticationContextDeclaration>
      <samlacpass:Identification nym="verinymity">
        <samlacpass:Extension>
          <safeac:NoVerification/>
        </samlacpass:Extension>
      </samlacpass:Identification>
    </samlacpass:AuthenticationContextDeclaration>
  </saml:AuthnContextDecl>
</saml:AuthnContext>
XML
        );

        /** @var \DOMElement $document->firstChild */
        $authnContext = AuthnContext::fromXML($document->firstChild);

        $contextDeclObj = $authnContext->getAuthnContextDecl();
        $this->assertInstanceOf(AuthnContextDecl::class, $contextDeclObj);

        $authnContextDecl = $contextDeclObj->getDecl();
        $this->assertEquals('samlacpass:AuthenticationContextDeclaration', $authnContextDecl->childNodes[1]->tagName);
    }
}
