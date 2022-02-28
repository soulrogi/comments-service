<?php

declare(strict_types=1);

namespace soulrogi\commentService;

use soulrogi\commentService\builder\Builder;
use soulrogi\commentService\models\Comment;
use soulrogi\commentService\phpDocs\CommentDoc;

class CommentsClientService {
	private const URL_PATH = '/comments';

	public function __construct(private Builder $builder) {}

	/**
	 * @return array<string, Comment>
	 */
	public function getAll(): array {
		$result  = [];
		$builder = $this->builder;
		$ok      = $builder->get()->setUrlPath(static::URL_PATH)->exec();
		if (false === $ok || [] !== $builder->getErrors() || null === $builder->getPayload()) {
			return $result;
		}

		foreach ($builder->getPayload() as $src) { /** @var CommentDoc $src */
			$comment = new Comment(
				$src->id,
				$src->name,
				$src->text,
			);

			$result[$comment->getId()] = $comment;
		}

		return $result;
	}

	public function getCommentById(string $getId): ?Comment {
		$builder = $this->builder;
		$ok      = $this->builder->get()->setUrlPath(static::URL_PATH)->setParams([Comment::ATTR_ID => $getId])->exec();
		if (false === $ok || [] !== $builder->getErrors() || null === $builder->getPayload()) {
			return null;
		}

		$src = $builder->getPayload();
		$src = reset($src); /** @var CommentDoc|bool $src */

		if (false === $src) {
			return null;
		}

		return new Comment(
			$src->id,
			$src->name,
			$src->text,
		);
	}

	public function addComment(Comment $comment): bool {
		return $this->builder
			->post()
			->setUrlPath(static::URL_PATH)
			->setParams($comment->toArray())
			->exec()
		;
	}

	public function updateComment(Comment $updateComment): bool {
		$comment = $this->getCommentById($updateComment->getId());
		if (null === $comment) {
			return false;
		}

		$requestComment = new Comment($updateComment->getId());

		($updateComment->getName() !== $comment->getName() && $requestComment->setName($updateComment->getName()));
		($updateComment->getText() !== $comment->getText() && $requestComment->setText($updateComment->getText()));

		return $this->builder
			->put()
			->setUrlPath(static::URL_PATH)
			->setParams($requestComment->toArray())
			->exec()
		;
	}
}
