<?php

namespace App\Services\Parser;

use App\Services\ConfigService;
use App\Interfaces\ParserInterface;

/**
 * Factory for instantiating the correct ParserInterface implementation.
 *
 * The factory reads the supported extension-to-class map from Config,
 * so adding a new format requires only a config entry — no code change here.
 */
class ParserFactoryService
{
    /**
     * @param ConfigService $config Application configuration
     */
    public function __construct(private readonly ConfigService $config)
    {
    }

    /**
     * Create a parser for the given file extension.
     *
     * @param  string          $extension File extension without leading dot (e.g. "csv")
     * @return ParserInterface
     *
     * @throws \InvalidArgumentException When no parser is registered for the extension
     * @throws \RuntimeException         When the configured class is not a valid ParserInterface
     */
    public function create(string $extension): ParserInterface
    {
        $extension = strtolower($extension);
        $parsers   = $this->config->getParsers();

        if (!array_key_exists($extension, $parsers)) {
            $supported = implode(', ', array_keys($parsers));
            throw new \InvalidArgumentException(
                "Unsupported file format \"{$extension}\". Supported: {$supported}."
            );
        }

        $className = $parsers[$extension];

        if (!class_exists($className)) {
            throw new \RuntimeException("Parser class \"{$className}\" not found.");
        }

        $parser = new $className();

        if (!$parser instanceof ParserInterface) {
            throw new \RuntimeException(
                "Class \"{$className}\" must implement ParserInterface."
            );
        }

        return $parser;
    }
}
