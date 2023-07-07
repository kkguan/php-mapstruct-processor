<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Option;

// TODO
use Psr\Log\LoggerInterface;

class Options
{
    private bool $suppressGeneratorTimestamp;

    private bool $suppressGeneratorVersionComment;

    private $unmappedTargetPolicy;

    private $unmappedSourcePolicy;

    private bool $alwaysGenerateSpi;

    private String $defaultComponentModel;

    private String $defaultInjectionStrategy;

    private bool $disableBuilders;

    private bool $verbose = false;

    private string $generatedSourcesDirectory;

    private LoggerInterface $logger;

    public function isSuppressGeneratorTimestamp(): bool
    {
        return $this->suppressGeneratorTimestamp;
    }

    public function setSuppressGeneratorTimestamp(bool $suppressGeneratorTimestamp): Options
    {
        $this->suppressGeneratorTimestamp = $suppressGeneratorTimestamp;
        return $this;
    }

    public function isSuppressGeneratorVersionComment(): bool
    {
        return $this->suppressGeneratorVersionComment;
    }

    public function setSuppressGeneratorVersionComment(bool $suppressGeneratorVersionComment): Options
    {
        $this->suppressGeneratorVersionComment = $suppressGeneratorVersionComment;
        return $this;
    }

    public function getUnmappedTargetPolicy()
    {
        return $this->unmappedTargetPolicy;
    }

    /**
     * @param mixed $unmappedTargetPolicy
     */
    public function setUnmappedTargetPolicy($unmappedTargetPolicy): Options
    {
        $this->unmappedTargetPolicy = $unmappedTargetPolicy;
        return $this;
    }

    public function getUnmappedSourcePolicy()
    {
        return $this->unmappedSourcePolicy;
    }

    /**
     * @param mixed $unmappedSourcePolicy
     */
    public function setUnmappedSourcePolicy($unmappedSourcePolicy): Options
    {
        $this->unmappedSourcePolicy = $unmappedSourcePolicy;
        return $this;
    }

    public function isAlwaysGenerateSpi(): bool
    {
        return $this->alwaysGenerateSpi;
    }

    public function setAlwaysGenerateSpi(bool $alwaysGenerateSpi): Options
    {
        $this->alwaysGenerateSpi = $alwaysGenerateSpi;
        return $this;
    }

    public function getDefaultComponentModel(): string
    {
        return $this->defaultComponentModel;
    }

    public function setDefaultComponentModel(string $defaultComponentModel): Options
    {
        $this->defaultComponentModel = $defaultComponentModel;
        return $this;
    }

    public function getDefaultInjectionStrategy(): string
    {
        return $this->defaultInjectionStrategy;
    }

    public function setDefaultInjectionStrategy(string $defaultInjectionStrategy): Options
    {
        $this->defaultInjectionStrategy = $defaultInjectionStrategy;
        return $this;
    }

    public function isDisableBuilders(): bool
    {
        return $this->disableBuilders;
    }

    public function setDisableBuilders(bool $disableBuilders): Options
    {
        $this->disableBuilders = $disableBuilders;
        return $this;
    }

    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    public function setVerbose(bool $verbose): Options
    {
        $this->verbose = $verbose;
        return $this;
    }

    public function getGeneratedSourcesDirectory(): string
    {
        return $this->generatedSourcesDirectory;
    }

    public function setGeneratedSourcesDirectory(string $generatedSourcesDirectory): Options
    {
        $this->generatedSourcesDirectory = $generatedSourcesDirectory;
        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): Options
    {
        $this->logger = $logger;
        return $this;
    }
}
