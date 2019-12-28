<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use SAML2\XML\AbstractConvertable;
use Webmozart\Assert\Assert;

/**
 * The Artifact is part of the SAML 2.0 IdP code, and it builds an artifact object.
 * I am using strings, because I find them easier to work with.
 * I want to use this, to be consistent with the other saml2_requests
 *
 * @author Danny Bollaert, UGent AS. <danny.bollaert@ugent.be>
 * @package SimpleSAMLphp
 */
class ArtifactResolve extends Request
{
    /** @var string */
    private $artifact;


    /**
     * Constructor for SAML 2 ArtifactResolve.
     *
     * @param \DOMElement|null $xml The input assertion.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('ArtifactResolve', $xml);

        if (!is_null($xml)) {
            $results = Utils::xpQuery($xml, './saml_protocol:Artifact');
            $this->artifact = $results[0]->textContent;
        }
    }

    /**
     * Retrieve the Artifact in this response.
     *
     * @return string artifact.
     *
     * @throws \InvalidArgumentException if assertions are false
     */
    public function getArtifact(): string
    {
        Assert::notEmpty($this->artifact, 'Artifact not set.');

        return $this->artifact;
    }


    /**
     * Set the artifact that should be included in this response.
     *
     * @param string $artifact
     * @return void
     */
    public function setArtifact(string $artifact): void
    {
        $this->artifact = $artifact;
    }


    /**
     * Convert XML into a ArtifactResolve
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        $results = Utils::xpQuery($xml, './saml_protocol:Artifact');
        $artifact = $results[0]->textContent;

        return new self($artifact);
    }


    /**
     * Convert the response message to an XML element.
     *
     * @return \DOMElement This response.
     *
     * @throws \InvalidArgumentException if assertions are false
     */
    public function toUnsignedXML(): DOMElement
    {
        Assert::notEmpty($this->artifact, 'Cannot convert ArtifactResolve to XML without an Artifact set.');

        $root = parent::toUnsignedXML();
        $artifactelement = $this->document->createElementNS(Constants::NS_SAMLP, 'Artifact', $this->artifact);
        $root->appendChild($artifactelement);

        return $root;
    }
}
