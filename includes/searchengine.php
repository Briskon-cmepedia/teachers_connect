<?php

use OpenSearch\Client;
use OpenSearch\ClientBuilder;

class SearchEngine
{
    const USERS_INDEX = 'users';
    const POSTS_INDEX = 'posts';
    private array $specialCharactersToRemoveInName = ['\'', '"', ',' , ';', '<', '>', '.', '-'];
    private Client $client;

    final public function __construct()
    {
            $this->client = $this->client ?? ClientBuilder::create()
                                    ->setHosts([LocalConfig::OPENSEARCH_SEARCH_HOST])  
                                    ->setBasicAuthentication(LocalConfig::OPENSEARCH_SEARCH_USER, LocalConfig::OPENSEARCH_SEARCH_PASSWORD)
                                    ->setSSLVerification(LocalConfig::OPENSEARCH_USE_SSL)
                                    ->build();
    }

    public static function boot(): SearchEngine
    {
        return new static();
    }

    public function search_filtered_posts_ordered_relevancy(string $keywords, array $groups, int $limit, ?int $offset = NULL, ?string $sort = null): array
    {
        try {
            $params = [
                'index' => 'posts',
                'from' => $offset,
                'size' => $limit,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                'match' => [ 
                                    'text' => [
                                        'query' => strtolower($keywords),
                                    ]
                                ]
                            ],
                            'should' => [
                                'match' => [
                                    'comments.text' => [
                                        'query' => strtolower($keywords)
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'sort' => [
                        '_score'
                    ]
                ]
            ];

            if (!is_null($sort)) {
                $params['body']['sort'] = [
                    [
                        'time.$date' => [
                          'order' => 'desc'
                        ]
                    ]
                ];
            }
    
            $result = $this->client->search($params)['hits']['hits'];
            $posts = [];
    
            foreach($result as $post) {
                array_push($posts, $post['_source']);
            }
            return $posts;
        } catch (\Throwable $e) {
            die($e->getMessage());
        }
    }

    public function search_members(string $keywords, int $limit, $offset = NULL) {
        $trimmedKeyword = trim($keywords);
        $keywordInternalMultiSpaceRemoved = preg_replace('/\s+/', ' ',$trimmedKeyword);

        try {
            $params = [
                'index' => 'users',
                'from' => $offset,
                'size' => $limit,
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => []
                        ]
                    ],
                    'sort' => [
                        '_score'
                    ]
                ]
            ];

            $keywords = str_replace($this->specialCharactersToRemoveInName, ' ', htmlspecialchars_decode($keywordInternalMultiSpaceRemoved, ENT_QUOTES));

            $params['body']['query']['bool']['should'] = [
                [
                    'match' => [
                        'firstNameAlias' => [
                            'query' => strtolower($keywords),
                        ]
                    ]
                ],
                [
                    'match' => [
                        'lastNameAlias' => [
                            'query' => strtolower($keywords),
                        ]
                    ]
                ]   
            ];

            $result = $this->client->search($params)['hits']['hits'];
            $users = [];
    
            foreach($result as $post) {
                array_push($users, $post['_source']);
            }
            return $users;
        } catch (\Throwable $e) {
        }
    }

    public function get_users(array $userIds): array
    {
        $users = [];

        try {
            $params = [
                'index' => 'users',
                'body' => ['ids' => $userIds]
            ];
            $result = $this->client->mget($params);
            foreach ($result['docs'] as $user) {
                $users[$user['_source']['id']] = $user['_source'];
            }
        } catch (\Throwable $e) {
            //throw $th;
        }

        return $users;
    }

    public function create_index(string $indexName, array $docBody): void
    {
        $docBody = json_decode(json_encode($docBody[0]),true);
        try {
            if (in_array('_id', $docBody)) {
                $docBody['id'] = $docBody['_id']['$oid'];
                unset($docBody['_id']);
                if ($indexName === self::USERS_INDEX) {

                    $firstNameAlias = str_replace($this->specialCharactersToRemoveInName, ' ', $docBody['firstName']);
                    $lastNameAlias = str_replace($this->specialCharactersToRemoveInName, ' ', $docBody['lastName']);
                    
                    $docBody['firstNameAlias'] = strtolower(preg_replace('/\s+/', ' ', trim($firstNameAlias)));
                    $docBody['lastNameAlias'] = strtolower(preg_replace('/\s+/', ' ', trim($lastNameAlias)));

                    unset($docBody['password']);
                }
            }

            $params =  [
                'index' => $indexName,
                'id' => $docBody['id'],
                'body' => $docBody
            ];
            $this->client->index($params);
        } catch (\Throwable $e) {
        }
    }

    public function delete_doc(string $indexName, string $docId): void
    {
        try {
            $params = [
                'index' => $indexName,
                'id' => $docId
            ];
            $this->client->delete($params);
        } catch (\Throwable $e) {
            //throw $th;
        }
    }

}