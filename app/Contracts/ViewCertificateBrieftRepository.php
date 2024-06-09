<?php

namespace App\Contracts;

interface ViewCertificateBrieftRepository extends BaseRepository
{
    public function countBrieftStatus();

    public function countBrieftStatusExpired();

    public function countBrieftStatusByAppraiser();

    public function countBrieftStatusByMonth();

    public function countBrieftBacklog();

    public function countBriefInProcessing();

    public function countBriefInProcessingPreCertificate();

    public function countBriefInProcessingCertificate();

    public function countBriefFinishByMonthCustomerGroup();

    public function countBriefConversionRateCustomerGroup();

    public function countBriefFinishByQuarters();

    public function countBriefCancelByQuarters();

    public function totalBriefBranchRevenue();

    public function totalBriefBranchDebt();

    public function countBriefFinishByMonth();

    public function countBriefCancelByMonth();
}
