<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\Utils;

/**
 * Class \SAML2\XML\saml\AuthenticatingAuthorityTest
 */
class AuthenticatingAuthorityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testMarshalling(): void
    {
        $authority = new AuthenticatingAuthority('https://sp.example.com/SAML2');

        $document = DOMDocumentFactory::fromString('<root />');
        /** @var \DOMElement $document->firstChild */
        $authorityElement = $authority->toXML($document->firstChild);

        $authorityElements = Utils::xpQuery(
            $authorityElement,
            '/root/saml_assertion:AuthenticatingAuthority'
        );
        $this->assertCount(1, $authorityElements);
        $auhthorityElement = $authorityElements[0];

        $this->assertEquals('https://sp.example.com/SAML2', $authorityElement->textContent);
    }


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $samlNamespace = Constants::NS_SAML;
        $document = DOMDocumentFactory::fromString(<<<XML
<saml:AuthenticatingAuthority xmlns:saml="{$samlNamespace}">
    https://sp.example.com/SAML2
</saml:AuthenticatingAuthority>
XML
        );

        /** @var \DOMElement $document->firstChild */
        $authority = AuthenticatingAuthority::fromXML($document->firstChild);
        $this->assertEquals('https://sp.example.com/SAML2', $authority->getAuthority());
    }
}
