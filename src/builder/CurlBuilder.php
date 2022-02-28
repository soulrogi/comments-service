<?php

declare(strict_types=1);

namespace soulrogi\commentService\builder;

class CurlBuilder implements Builder {
	/** @var mixed */
	private $payload;

	private string $urlPath;

	private string $methodType;

	/** @var string[] */
	private array $errors  = [];

	/** @var array<string, string> */
	private array $params  = [];

	/** @var string[] Массив заголовков */
	private array $headers = [];

	public function __construct(private string $serviceUrl) {}

	public function post(): static {
		$this->methodType = static::METHOD_TYPE_POST;

		return $this;
	}

	public function get(): static {
		$this->methodType = static::METHOD_TYPE_GET;

		return $this;
	}

	public function put(): static {
		$this->methodType = static::METHOD_TYPE_PUT;

		return $this;
	}

	public function setUrlPath(string $path): static {
		$this->urlPath = $path;

		return $this;
	}

	public function setParams(array $params): static {
		$this->params = $params;

		return $this;
	}

	public function setHeaders(array $headers): static {
		$this->headers = array_merge($this->headers, $headers);

		return $this;
	}

	public function getPayload() {
		return $this->payload;
	}

	public function getErrors(): array {
		return $this->errors;
	}

	public function exec(): bool {
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_HTTPHEADER     => $this->headers,
			CURLOPT_CUSTOMREQUEST  => strtoupper($this->methodType),
			CURLOPT_ENCODING       => 'UTF-8',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_TIMEOUT        => 15,
		]);

		if (static::METHOD_TYPE_GET === $this->methodType) {
			curl_setopt($curl, CURLOPT_URL, $this->serviceUrl . $this->urlPath . '?' . http_build_query($this->params));
		}
		else {
			curl_setopt_array($curl, [
				CURLOPT_URL        => $this->serviceUrl . $this->urlPath,
				CURLOPT_POSTFIELDS => json_encode($this->params),
			]);
		}

		$src       = curl_exec($curl);
		$httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$errorCode = curl_errno($curl);

		curl_close($curl);

		if (static::EXPECTED_CODE !== $httpCode) {
			$this->errors[] = 'Код ответа отличаеться от ожидаемого (' . $httpCode . ')';

			return false;
		}

		if (false === $src) {
			$this->errors[] = 'Код ошибки: ' . $errorCode;

			return false;
		}

		$result = json_decode($src);
		if (null === $result) {
			$this->errors[] = 'Не удалось разобрать ответ!';

			return false;
		}

		$this->payload = $result;

		return true;
	}
}
