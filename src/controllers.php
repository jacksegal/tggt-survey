<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bsd\FormBuilder\BsdApi;
use GreatGetTogether\Slack;
use Google\Cloud\Datastore\DatastoreClient;

/*$app->get('/qoiqwdqwdhqwiodhqoiwdioqwd/', function (Request $request) use ($app) {

    $twig = $app['twig'];

    return $twig->render('base.html.twig', array());
});*/

$app->get('/qoiqwdqwdhqwiodhqoiwdioqwd/js', function (Request $request) use ($app) {

    $twig = $app['twig'];

    return $twig->render('js.html.twig', array());
});

/*$app->get('/qoiqwdqwdhqwiodhqoiwdioqwd/embed', function (Request $request) use ($app) {

    $twig = $app['twig'];

    return $twig->render('sidebar.html.twig', array());
});*/

/*$app->get('/qoiqwdqwdhqwiodhqoiwdioqwd/embed/h', function (Request $request) use ($app) {

    $twig = $app['twig'];

    return $twig->render('horizontal.html.twig', array());
});*/

$app->post('/api/ijqw7dx/signup/{form}', function (Request $request, $form) use ($app) {


    /**
     * Load Config & Initiate BSD Class
     */

    $config = [
        "urlRoot" => $app['config']['bsd_url_root'],
        "appSecret" => $app['config']['bsd_app_secret'],
        "appId" => $app['config']['bsd_app_id'],
    ];

    $bsdApi = new \Bsd\FormBuilder\BsdApi($config);


    /**
     * Place Request Data into format for Processing
     */

    $signupFields = $request->request->all();
    $unsub = false;

    foreach ($signupFields as $key => $value) {

        if ($key == 'unsub') {
            $unsub = $value;
        } else {
            $signupData[] = [
                "id" => $key,
                "value" => $value,
            ];
        }

    }


    /**
     * Send to BSD API and Return Response
     */

    $signupResponse = $bsdApi->process_signup($form, $signupData);

    if ($signupResponse['http'] == 200) {

        if ($unsub) {
            // Let the signup get processed before sending unsub
            sleep(2);
            $unsubResponse = $bsdApi->email_unsubscribe($unsub, 'PledgeCheckBox');

            if ($unsubResponse['http'] == 200) {

            } else {

                sleep(5);
                $unsubResponse = $bsdApi->email_unsubscribe($unsub, 'PledgeCheckBox');

                if ($unsubResponse['http'] == 200) {

                } else {

                    sleep(10);
                    $unsubResponse = $bsdApi->email_unsubscribe($unsub, 'PledgeCheckBox');

                    if ($unsubResponse['http'] == 200) {

                    } else {

                        sleep(30);
                        $unsubResponse = $bsdApi->email_unsubscribe($unsub, 'PledgeCheckBox');

                        if ($unsubResponse['http'] == 200) {

                        } else {

                            // Instantiates a client
                            $datastore = new DatastoreClient([
                                'projectId' => $app['config']['google_project_id']
                            ]);

                            // Add New Records
                            $failure = $datastore->entity('Failed', [
                                'form' => $form,
                                'data' => $unsub,
                                'unsub' => $unsub ? 'true' : 'false',
                                'reponse' => $unsubResponse,
                                'date' => date('Y-m-d H:i:s'),
                            ]);
                            $datastore->insert($failure);
                            $slack = new \GreatGetTogether\Slack();
                            $slack->failedUnsub($form, json_encode($unsub), json_encode($unsubResponse));
                        }
                    }
                }
            }

            return $app->json($unsubResponse, 200);
        }

    } else {
        // Instantiates a client
        $datastore = new DatastoreClient([
            'projectId' => $app['config']['google_project_id']
        ]);

        // Add New Records
        $failure = $datastore->entity('Failed', [
            'form' => $form,
            'data' => $signupData,
            'unsub' => $unsub ? 'true' : 'false',
            'reponse' => $signupResponse,
            'date' => date('Y-m-d H:i:s'),
        ]);
        $datastore->insert($failure);

        $slack = new \GreatGetTogether\Slack();
        $slack->failedSignup($form, json_encode($signupData), json_encode($signupResponse));
    }

    return $app->json($signupResponse, 200);

});

$app->get('/failures', function (Request $request) use ($app) {

    // Instantiates a client
    $datastore = new DatastoreClient([
        'projectId' => $app['config']['google_project_id']
    ]);

    // Create Query
    $query = $datastore->query()
        ->kind('Failed');

    // Run Query
    $results = $datastore->runQuery($query);

    // Get Result Set
    $failures = [];
    foreach ($results as $entity) {
        $failure = $entity->get();
        $failure['id'] = $entity->key()->pathEndIdentifier();
        $failures[] = $failure;
    }

    return $app->json($failures, 200);
});

/*$app->get('/unsub', function (Request $request) use ($app) {

    $config = [
        "urlRoot" => $app['config']['bsd_url_root'],
        "appSecret" => $app['config']['bsd_app_secret'],
        "appId" => $app['config']['bsd_app_id'],
    ];

    $bsdApi = new \Bsd\FormBuilder\BsdApi($config);

    $unsubResponse = $bsdApi->email_unsubscribe('segal_jack@yahoo.com', 'PledgeCheckBox');

    return $app->json($unsubResponse, 200);
});*/

/*$app->get('/slack', function (Request $request) use ($app) {

    $slack = new \GreatGetTogether\Slack();
    $response = $slack->failedSignup("{example data}");

    return $app->json($response, 200);
});*/