<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '0.0.1',
    description: 'Personal Accounting API empowers authorized users to seamlessly manage their personal finances by recording income and expenses.
                  This API allows users to create, retrieve, and analyze financial transactions, providing a comprehensive solution for personal accounting.
                  With secure authentication, intuitive endpoints, and insightful data summaries, users can easily track their financial activities, view transaction lists, and obtain cumulative amounts for both income and expenses.
                  Simplify personal financial management with the capabilities of the Personal Accounting API.',
    title: 'Accounting API')]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
