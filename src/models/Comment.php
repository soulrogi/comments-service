<?php

declare(strict_types=1);

namespace soulrogi\commentService\models;

use Ramsey\Uuid\Uuid;

class Comment {
	private string $id;
	public const ATTR_ID   = 'id';

	private ?string $name;
	public const ATTR_NAME = 'name';

	private ?string $text;
	public const ATTR_TEXT = 'text';

	public function __construct(?string $id = null, ?string $name = null, ?string $text = null ) {
		$this->id   = ($id ?? Uuid::uuid4()->toString());
		$this->name = $name;
		$this->text = $text;
	}

	public function getId(): ?string {
		return $this->id;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function getText(): ?string {
		return $this->text;
	}

	public function setName(string $name): static {
		$this->name = $name;

		return $this;
	}

	public function setText(string $text): static {
		$this->text = $text;

		return $this;
	}

	public function isEquals(self $comment): bool {
		return ($this->getId() === $comment->getId());
	}

	public function toArray(): array {
		return array_filter([
			static::ATTR_ID   => $this->getId(),
			static::ATTR_NAME => $this->getName(),
			static::ATTR_TEXT => $this->getText(),
		]);
	}
}
