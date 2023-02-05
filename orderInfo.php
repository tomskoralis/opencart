<?php

class Order
{
    private OrderInfo $info;
    private Customer $customer;
    private OrderDetails $payment;
    private OrderDetails $shipping;

    public function __construct(
        OrderInfo    $info,
        Customer     $customer,
        OrderDetails $payment,
        OrderDetails $shipping
    )
    {
        $this->info = $info;
        $this->customer = $customer;
        $this->payment = $payment;
        $this->shipping = $shipping;
    }

    public function info(): OrderInfo
    {
        return $this->info;
    }

    public function customer(): Customer
    {
        return $this->customer;
    }

    public function payment(): OrderDetails
    {
        return $this->payment;
    }

    public function shipping(): OrderDetails
    {
        return $this->shipping;
    }
}

class OrderInfo
{
    private string $id;
    private string $invoice;
    private string $comment;
    private string $total;
    private string $currency;
    private string $status;
    private string $commission;
    private string $language;
    private string $ip;
    private string $browser;
    private string $createdAt;
    private string $modifiedAt;

    public function __construct(
        string $id,
        string $invoice,
        string $comment,
        string $total,
        string $currency,
        string $status,
        string $commission,
        string $language,
        string $ip,
        string $browser,
        string $createdAt,
        string $modifiedAt
    )
    {
        $this->id = $id;
        $this->invoice = $invoice;
        $this->comment = $comment;
        $this->total = $total;
        $this->currency = $currency;
        $this->status = $status;
        $this->commission = $commission;
        $this->language = $language;
        $this->ip = $ip;
        $this->browser = $browser;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
    }

    public function getDetails(): string
    {
        $order = $this->id ? "Order ID: $this->id\n" : "";
        $order .= $this->invoice ? "Invoice: $this->invoice\n" : "";
        $order .= $this->comment ? "Comment: $this->comment\n" : "";
        $order .= (float)$this->total > 0
            ? "Total: " . number_format($this->total, 2) . " $this->currency\n"
            : "";
        $order .= $this->status ? "Status : $this->status\n" : "";
        $order .= (float)$this->commission > 0
            ? "Commission: " . number_format($this->commission, 2) . " $this->currency\n"
            : "";
        $order .= $this->language ? "Language: $this->language\n" : "";
        $order .= $this->ip ? "IP: $this->ip\n" : "";
        $order .= $this->browser ? "Browser: $this->browser\n" : "";
        $order .= $this->createdAt ? "Created at : $this->createdAt\n" : "";
        $order .= $this->modifiedAt ? "Modified at: $this->modifiedAt\n" : "";
        return $order;
    }
}

class OrderDetails
{
    private string $firstName;
    private string $lastName;
    private string $company;
    private string $address1;
    private string $address2;
    private string $postcode;
    private string $city;
    private string $zone;
    private string $country;
    private string $method;

    public function __construct(
        string $firstName,
        string $lastName,
        string $company,
        string $address1,
        string $address2,
        string $postcode,
        string $city,
        string $zone,
        string $country,
        string $method
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->company = $company;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->zone = $zone;
        $this->country = $country;
        $this->method = $method;
    }

    public function getDetails(): string
    {
        $details = $this->firstName || $this->lastName
            ? "Name: " . implode(' ', [$this->firstName, $this->lastName]) . " \n"
            : "";
        $details .= $this->company ? "Company: $this->company\n" : "";
        $details .= $this->address1 ? "Address: $this->address1\n" : "";
        $details .= $this->address2 ? "Address 2: $this->address2\n" : "";
        $details .= $this->postcode ? "Postcode: $this->postcode\n" : "";
        $details .= $this->city ? "City: $this->city\n" : "";
        $details .= $this->zone ? "Zone: $this->zone\n" : "";
        $details .= $this->country ? "Country: $this->country\n" : "";
        $details .= $this->method ? "Method: $this->method\n" : "";
        return $details;
    }
}

class Customer
{
    private string $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $telephone;
    private string $fax;

    public function __construct(
        string $id,
        string $firstName,
        string $lastName,
        string $email,
        string $telephone,
        string $fax
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->fax = $fax;
    }

    public function getDetails(): string
    {
        $customer = $this->id ? "Customer ID: $this->id\n" : "";
        $customer .= $this->firstName || $this->lastName
            ? "Name: " . implode(' ', [$this->firstName, $this->lastName]) . " \n"
            : "";
        $customer .= $this->email ? "E-mail: $this->email\n" : "";
        $customer .= $this->telephone ? "Telephone: $this->telephone\n" : "";
        $customer .= $this->fax ? "Fax: $this->fax\n" : "";
        return $customer;
    }
}

function curlRequest(string $url, array $postBody = [])
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => implode('&', $postBody),
        CURLOPT_COOKIE => 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/',
        CURLOPT_HTTPHEADER => [
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

function getOrderInfo(string $apiKey, string $storeUrl, int $orderId): string
{
    $url = "$storeUrl/index.php?route=api/login";
    $postBody = ["key=$apiKey"];
    $response = curlRequest($url, $postBody);
    if (empty($response) || !isset($response->token)) {
        return "Warning: Incorrect API Key!\n";
    }
    if (isset($response->error)) {
        return "$response->error\n";
    }

    $token = $response->token;
    $url = "$storeUrl/index.php?route=api/order/info&token=$token&order_id=$orderId";
    $response = curlRequest($url);
    if (empty($response)) {
        return "Couldn't gather the order data!\n";
    }
    if (isset($response->error)) {
        return "$response->error\n";
    }

    $order = new Order(
        new OrderInfo(
            $response->order->order_id,
            $response->order->invoice_prefix,
            $response->order->comment,
            $response->order->total,
            $response->order->currency_code,
            $response->order->order_status,
            $response->order->commission,
            $response->order->language_code,
            $response->order->ip,
            $response->order->user_agent,
            $response->order->date_added,
            $response->order->date_modified
        ),
        new Customer(
            $response->order->customer_id,
            $response->order->firstname,
            $response->order->lastname,
            $response->order->email,
            $response->order->telephone,
            $response->order->fax,
        ),
        new OrderDetails(
            $response->order->payment_firstname,
            $response->order->payment_lastname,
            $response->order->payment_company,
            $response->order->payment_address_1,
            $response->order->payment_address_2,
            $response->order->payment_postcode,
            $response->order->payment_city,
            $response->order->payment_zone,
            $response->order->payment_country,
            $response->order->payment_method,
        ),
        new OrderDetails(
            $response->order->shipping_firstname,
            $response->order->shipping_lastname,
            $response->order->shipping_company,
            $response->order->shipping_address_1,
            $response->order->shipping_address_2,
            $response->order->shipping_postcode,
            $response->order->shipping_city,
            $response->order->shipping_zone,
            $response->order->shipping_country,
            $response->order->shipping_method,
        )
    );

    $info = "Order details:\n";
    $info .= $order->info()->getDetails();
    $info .= "\nCustomer details:\n";
    $info .= $order->customer()->getDetails();
    $info .= "\nPayment details:\n";
    $info .= $order->payment()->getDetails();
    $info .= "\nShipping details:\n";
    $info .= $order->shipping()->getDetails();

    return $info;
}

const STORE_URL = 'http://localhost:8000';
$apiKey = readline('API key: ');
$orderId = (int)readline('Order ID: ');

session_start();
$_COOKIE['PHPSESSID'] = session_id();
session_write_close();

echo getOrderInfo($apiKey, STORE_URL, $orderId);