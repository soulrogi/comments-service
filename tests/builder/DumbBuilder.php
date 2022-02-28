<?php

namespace tests\builder;

use soulrogi\commentService\builder\Builder;
use soulrogi\commentService\models\Comment;
use stdClass;

class DumbBuilder implements Builder {
	/** @var array<string, array> */
	private array  $store = [];

	private string $type;

	public function __construct(private ?string $serviceUrl = null) {}

	public function post(): static {
		$this->type = static::METHOD_TYPE_POST;

		return $this;
	}

	public function get(): static {
		$this->type = static::METHOD_TYPE_GET;

		return $this;
	}

	public function put(): static {
		$this->type = static::METHOD_TYPE_PUT;

		return $this;
	}

	public function setUrlPath(string $path): static {
		return $this;
	}

	public function setParams(array $params): static {
		switch ($this->type) {
			case static::METHOD_TYPE_POST:
				$this->store[$params[Comment::ATTR_ID]] = $params;

				break;
			case static::METHOD_TYPE_PUT:
				if (array_key_exists(Comment::ATTR_NAME, $params)) {
					$this->store[$params[Comment::ATTR_ID]][Comment::ATTR_NAME] = $params[Comment::ATTR_NAME];
				}

				if (array_key_exists(Comment::ATTR_TEXT, $params)) {
					$this->store[$params[Comment::ATTR_ID]][Comment::ATTR_TEXT] = $params[Comment::ATTR_TEXT];
				}

				break;
		}

		return $this;
	}

	public function setHeaders(array $headers): static {
		return $this;
	}

	public function getPayload() {
		return array_map(fn(array $item): stdClass => (object) $item , $this->store);
	}

	public function getErrors(): array {
		return [];
	}

	public function exec(): bool {
		return true;
	}
}
