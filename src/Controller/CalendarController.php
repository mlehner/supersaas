<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

final class CalendarController extends AbstractController
{
    public function appointments(string $superSaasScheduleId, string $superSaasApiCredentials, Validator $validator): Response
    {
        $superSaasApiUrl = "range/$superSaasScheduleId.json";

        $addHeader = function ($header, $value) {
            return function (callable $handler) use ($header, $value) {
                return function (
                    RequestInterface $request,
                    array $options
                ) use ($handler, $header, $value) {
                    $request = $request->withHeader($header, $value);
                    return $handler($request, $options);
                };
            };
        };

        $handlerStack = HandlerStack::create();
        $handlerStack->push($addHeader('Authorization', 'Basic ' . base64_encode($superSaasApiCredentials)));

        $client = new Client([
            'base_uri' => 'https://www.supersaas.com/api/',
            'handler' => $handlerStack,
        ]);

        $apiResponse = $client->get($superSaasApiUrl . '?slot=true&from=' . date('Y-m-d'));

        if ($apiResponse->getStatusCode() !== 200) {
            return Response::create('API ERROR', 500);
        }

        $jsonResponse = json_decode((string) $apiResponse->getBody(), false, 10, JSON_THROW_ON_ERROR);

        $validator->resolver()->registerFile('http://supersaas.com/api/range.json', __DIR__ . '/../../json_schemas/GetAppointments.json');

        $result = $validator->validate($jsonResponse, 'http://supersaas.com/api/range.json');

        if (!$result->isValid()) {
            return Response::create(json_encode((new ErrorFormatter())->format($result->error()), JSON_THROW_ON_ERROR), 500);
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile(__DIR__ . '/../../var/tmp/' . uniqid() . '.json', $apiResponse->getBody());

        $appointments = $this->flattenAppointments($jsonResponse);

        return Response::create(json_encode(iterator_to_array($appointments), JSON_THROW_ON_ERROR));
    }

    private function flattenAppointments(object $response): iterable {
        foreach ($response->slots ?? [] as $slot) {
            yield [
                'id' => $slot->id,
                'title' => $slot->title,
                'capacity' => $slot->capacity,
                'start' => $slot->start,
                'end' => $slot->finish,
                'bookings' => $this->normalizeBookings($slot->bookings ?? []),
            ];
        }
    }

    private function normalizeBookings(array $bookings): iterable {
        $normalized = [];

        foreach ($bookings as $booking) {
            $normalized[] = [
                'id' => $booking->id,
                'full_name' => $booking->full_name,
            ];
        }

        return $normalized;
    }
}