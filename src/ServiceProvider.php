<?php

namespace Amsoell\AssertJsonStructureMissing;

use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        TestResponse::macro('assertJsonStructureMissing', function (array $structure = null, $responseData = null) {
            if (is_null($structure)) {
                return $this->assertExactJson($this->json());
            }

            if (is_null($responseData)) {
                $responseData = $this->decodeResponseJson();
            }

            foreach ($structure as $key => $value) {
                if (is_array($value) && $key === '*') {
                    Assert::assertIsArray($responseData);

                    foreach ($responseData as $responseDataItem) {
                        $this->assertJsonStructureMissing($structure['*'], $responseDataItem);
                    }
                } elseif (is_array($value)) {
                    Assert::assertArrayHasKey($key, $responseData);

                    $this->assertJsonStructureMissing($structure[$key], $responseData[$key]);
                } else {
                    Assert::assertArrayNotHasKey($value, $responseData);
                }
            }

            return $this;
        });
    }
}
