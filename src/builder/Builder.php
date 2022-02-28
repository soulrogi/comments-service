<?php

declare(strict_types=1);

namespace soulrogi\commentService\builder;

interface Builder {
	public const METHOD_TYPE_POST = 'post';
	public const METHOD_TYPE_GET  = 'get';
	public const METHOD_TYPE_PUT  = 'put';

	public const EXPECTED_CODE = 200;

	public function __construct(string $serviceUrl);

	public function post(): static;

	public function get(): static;

	public function put(): static;

	public function setUrlPath(string $path): static;

	public function setParams(array $params): static;

	public function setHeaders(array $headers): static;

	/**
	 * @return mixed
	 */
	public function getPayload();

	public function getErrors(): array;

	public function exec(): bool;
}
