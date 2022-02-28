<?php

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use soulrogi\commentService\CommentsClientService;
use soulrogi\commentService\models\Comment;
use tests\builder\DumbBuilder;

final class CommentsClientServiceTest extends TestCase {
	public function testCreateService(): void {
		static::assertInstanceOf(CommentsClientService::class, new CommentsClientService(new DumbBuilder));
	}

	public function testGetComments(): void {
		$service = new CommentsClientService(new DumbBuilder);
		$service->addComment(
			new Comment(
				Uuid::uuid4()->toString(),
				Uuid::uuid4()->toString(),
				Uuid::uuid4()->toString(),
			)
		);
		$service->addComment(
			new Comment(
				Uuid::uuid4()->toString(),
				Uuid::uuid4()->toString(),
				Uuid::uuid4()->toString(),
			)
		);

		$comments = $service->getAll();

		static::assertIsArray($comments);

		$comment = reset($comments);
		static::assertInstanceOf(Comment::class, $comment);
	}

	public function testAddComment(): void {
		$result = (new CommentsClientService(new DumbBuilder))->addComment(new Comment(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
		));

		static::assertTrue($result);
	}

	public function testCommentsIsEquals(): void {
		$commentOne = new Comment(
			$id = Uuid::uuid4()->toString(),
			$name = Uuid::uuid4()->toString(),
			$text = Uuid::uuid4()->toString(),
		);

		$commentTwo = new Comment($id, $name, $text);

		static::assertTrue($commentOne->isEquals($commentTwo));

		$commentOne = new Comment(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
		);

		$commentTwo = new Comment(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
		);

		static::assertFalse($commentOne->isEquals($commentTwo));

	}

	public function testUpdateComment(): void {
		$service = new CommentsClientService(new DumbBuilder);

		$service->addComment($comment = new Comment(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
		));

		$createdComment = $service->getCommentById($comment->getId());
		static::assertNotNull($createdComment);
		static::assertEquals($createdComment, $comment);

		$updateComment = clone $comment;
		$updateComment
			->setName(Uuid::uuid4()->toString())
			->setText(Uuid::uuid4()->toString())
		;

		$service->updateComment($updateComment);

		$commentResponse = $service->getCommentById($comment->getId());

		static::assertNotNull($commentResponse);
		static::assertEquals($commentResponse, $updateComment);
	}
}
