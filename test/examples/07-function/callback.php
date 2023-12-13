<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function processCode()
{
    // Create SDK instance
    $config = include('config.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['oauth_redirect_uri'],
        'scope' => $config['oauth_scope'],
        'baseUrl' => "development"
    ));

    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $parseUrl = parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

    try {
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
        $dataService->updateOAuth2Token($accessToken);

        $_SESSION['sessionAccessToken'] = $accessToken;
        // Redirect to a different page or show a success message
    } catch (Exception $e) {
        // Handle error
        echo "Error: " . $e->getMessage();
    }
}

function parseAuthRedirectUrl($url)
{
    parse_str($url, $qsArray);
    return array(
        'code' => $qsArray['code'] ?? null,
        'realmId' => $qsArray['realmId'] ?? null
    );
}

processCode();

?>
