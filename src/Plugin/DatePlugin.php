<?php


namespace App\Plugin;


use Carbon\Carbon;

class DatePlugin
{
    const NORMALIZED_DATE_FORMAT = "Y-m-d H:i:s A";
    const NORMALIZED_TIME_FORMAT = "H:i:s A";

    public function getNormalizedDate(\DateTimeInterface $dateTime): ?string
    {
        return Carbon::createFromTimestamp($dateTime->getTimestamp())->toDayDateTimeString();
//        return $dateTime->format(self::NORMALIZED_DATE_FORMAT);
    }

    public function getNormalizedTime(\DateTimeInterface $dateTime): ?string
    {
        return $dateTime->format(self::NORMALIZED_TIME_FORMAT);
    }

    public function getExpiryDate(\DateTimeInterface $dateTime, int $duration) {
        $dateExpiry = date_add($dateTime, date_interval_create_from_date_string($duration . ' days'));

        return $dateExpiry;
    }

    public function getFriendlyDateAgo(\DateTimeInterface $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        return Carbon::createFromTimestamp($timestamp)->ago();
    }

}