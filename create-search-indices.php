<?php
// System Setup
require 'includes/startup.php';

error_reporting(E_ALL);
ini_set("display_errors", "On");
ini_set('memory_limit', '512M');

$client =  (new OpenSearch\ClientBuilder())
            ->setHosts([LocalConfig::OPENSEARCH_SEARCH_HOST])  
            ->setBasicAuthentication(LocalConfig::OPENSEARCH_SEARCH_USER, LocalConfig::OPENSEARCH_SEARCH_PASSWORD)
            ->setSSLVerification(LocalConfig::OPENSEARCH_USE_SSL)
            ->build();

echo '<br/><br/>';
echo '<form action=""> <input type="hidden" value="true" name="updateReactions"> <button type="submit">Update Reactions</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="updateCommentReactions"> <button type="submit">Update CommentReactions</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="updateFollowing"> <button type="submit">Update Following</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="updateAccessKey"> <button type="submit">Update AccessKey</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="updateTopicsFollowed"> <button type="submit">Update TopicsFollowed</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="updateEmailVerifiedAt"> <button type="submit">Update EmailVerifiedAt</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="indexUsers"> <button type="submit">Index Users</button></form>';
echo '<form action=""> <input type="hidden" value="true" name="indexPosts"> <button type="submit">Index Posts</button></form>';

try {
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

    if (isset($_GET['updateReactions']) && $_GET['updateReactions'] == true) {
        echo "<h4>Updating reactions from object to array...</h4>";
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
                        '$or' => [
                            [
                                "reactions.highfive" => [
                                        '$type' => 'object'
                                ],
                            ],
                            [
                                "reactions.thumbsup" => [
                                    '$type' => 'object'
                                ]
                            ]
                        ]                        
                ]);
        $collection = $mongo->executeQuery('tc.posts', $query);
        $posts = [];
        foreach ($collection as $document) {
            array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach($posts as $post) {
            $highfiveArr = json_decode(json_encode( $post->reactions->highfive), true);
            $thumbsupArr = json_decode(json_encode( $post->reactions->thumbsup), true);
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($post->_id->{'$oid'})],
                ['$set' =>
                    [
                        'reactions' => [
                            'highfive' => array_values($highfiveArr),
                            'thumbsup' => array_values($thumbsupArr)
                        ],
                    ]
                ],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.posts', $bulk);
        echo 'Reaction updated';
    }

    if (isset($_GET['updateCommentReactions']) && $_GET['updateCommentReactions'] == true) {
        echo "<h4>Updating comment reactions from object to array...</h4>";
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
                        '$or' => [
                            [
                                "comments.reactions.highfive" => [
                                        '$type' => 'object'
                                ],
                            ],
                            [
                                "comments.reactions.thumbsup" => [
                                    '$type' => 'object'
                                ]
                            ]
                        ]                        
                ]);
        $collection = $mongo->executeQuery('tc.posts', $query);
        $posts = [];
        foreach ($collection as $document) {
            array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;

        foreach($posts as $post) {
            $postComments = $post->comments;

            $updatedComments = array_map(function($comment) {
                $commentToUpdate = $comment;
                $highfiveArr = json_decode(json_encode( $commentToUpdate->reactions->highfive), true);
                $thumbsupArr = json_decode(json_encode( $commentToUpdate->reactions->thumbsup), true);
               
                $commentToUpdate->reactions->highfive = array_values($highfiveArr);
                $commentToUpdate->reactions->thumbsup = array_values($thumbsupArr);

                return $commentToUpdate;

            }, $postComments);

            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($post->_id->{'$oid'})],
                ['$set' =>
                    [
                        'comments' => $updatedComments
                    ]
                ],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.posts', $bulk);
        echo 'Post Comments Reaction updated';
    }

    if (isset($_GET['updateAccessKey']) && $_GET['updateAccessKey'] == true) {
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
            'access' => [
                '$type' => 'bool'
            ]
        ]);
        $collection = $mongo->executeQuery('tc.users', $query);
        $users = [];
        foreach ($collection as $document) {
            array_push($users, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($users as $user) {
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($user->_id->{'$oid'})],
                ['$set' => [
                    'access' => $user->access ? 1 : 0
                ]],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.users', $bulk);
        echo 'Finished updating access key to number';
    }

    if (isset($_GET['updateTopicsFollowed']) && $_GET['updateTopicsFollowed'] == true) {
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
            'topicsFollowed' => [
                '$type' => 'object'
            ]
        ]);
        $collection = $mongo->executeQuery('tc.users', $query);
        $users = [];
        foreach ($collection as $document) {
            array_push($users, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($users as $user) {
            $topicsFollowedArr = json_decode(json_encode($user->topicsFollowed), true);
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($user->_id->{'$oid'})],
                ['$set' => [
                    'topicsFollowed' => array_values($topicsFollowedArr)
                ]],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.users', $bulk);
        echo 'Finished updating topicsFollowed';

    }

    if (isset($_GET['updateFollowing']) && $_GET['updateFollowing'] == true) {
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
            'following' => [
                '$type' => 'object'
            ]
        ]);
        $collection = $mongo->executeQuery('tc.posts', $query);
        $posts = [];
        foreach ($collection as $document) {
            array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($posts as $post) {
            $followingArr = json_decode(json_encode($post->following), true);
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($post->_id->{'$oid'})],
                ['$set' => [
                    'following' => array_values($followingArr)
                ]],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.posts', $bulk);
        echo 'Finished updating post following';
    }

    if (isset($_GET['updateEmailVerifiedAt']) && $_GET['updateEmailVerifiedAt'] == true) {
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([
            'emailVerifiedAt' => ''
        ]);
        $collection = $mongo->executeQuery('tc.users', $query);
        $users = [];
        foreach ($collection as $document) {
            array_push($users, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($users as $user) {
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectID($user->_id->{'$oid'})],
                ['$set' => [
                    'emailVerifiedAt' => null
                ]],
                [
                    'multi' => false,
                    'upsert' => false
                ]
            );
        }
        $mongo->executeBulkWrite('tc.users', $bulk);
        echo 'Email verification set to null where it was empty';

    }

    if(isset($_GET['indexUsers']) && $_GET['indexUsers'] == true) {
        $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
        $query = new MongoDB\Driver\Query([], []);
        $cursor = $mongo->executeQuery('tc.users', $query);
        $usersDocuments = [];
        foreach ($cursor as $document) {
                $documentConvertedToArray = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document)));
                $userDataArray = json_decode(json_encode($documentConvertedToArray), true);
              
                $specialCharactersToRemoveInName = ['\'', '"', ',' , ';', '<', '>', '.','-'];
                $documentConvertedToArray->firstNameAlias = str_replace($specialCharactersToRemoveInName, ' ', $documentConvertedToArray->firstName);
                $documentConvertedToArray->lastNameAlias = str_replace($specialCharactersToRemoveInName, ' ',  $documentConvertedToArray->lastName);

                array_push($usersDocuments, $documentConvertedToArray);
        }

        $users = json_decode(json_encode($usersDocuments), true);

        $params = [
            'body' => []
        ];
        $counter = 0;
        $responses = [];
        foreach ($users as $user) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'users',
                    '_id' => $user['_id']['$oid']
                ]
            ];

            $user = array_reverse($user);
            $user['id'] = $user['_id']['$oid'];
            $user = array_reverse($user);

            unset($user['_id'], $user['password']);
            $params['body'][] = $user;

            if ($counter > 0 && ($counter % 1000) == 0) {
                $response = $client->bulk($params);
                array_push($responses, $response);
                $params = ['body' => []];
            }
            $counter++;
        }

        if(!empty($params['body'])){
            $response = $client->bulk($params);
            array_push($responses, $response);
        }
        die(json_encode($responses));
        echo "Users Index Completed";

    }

    if (isset($_GET['indexPosts']) && $_GET['indexPosts'] == true) {
        echo "<h4>Indexing all posts...</h4>";

        $posts = json_decode(json_encode(get_all_posts()), true);
        $params = ['body' => []];
        $counter = 0;
        $responses = [];
        foreach($posts as $post) {
            $comments = [];
            foreach ($post['comments'] as $comment) {
                $comments[] = [
                    'id' => $comment['_id']['$oid'],
                    'userId' => $comment['userId'],
                    'text' => $comment['text'],
                    'time' => $comment['time']['$date']
                ];
            }

            $params['body'][] = [
                'index' => [
                    '_index' => 'posts',
                    '_id' => $post['_id']['$oid']
                ]
            ];
            
            $post = array_reverse($post);
            $post['id'] = $post['_id']['$oid'];
            $post = array_reverse($post);

            unset($post['_id']);

            $params['body'][] = $post;

            if (($counter % 1000) == 0) {
                $response = $client->bulk($params);
                array_push($responses, $response);
                $params = ['body' => []];
            }
            $counter++;
        }

        if(!empty($params['body'])){
            $response = $client->bulk($params);
            array_push($responses, $response);
        }
        die(json_encode($responses));
        echo "Post Index Completed";

    }

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
