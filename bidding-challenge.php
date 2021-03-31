<?php 

class Bidding
{

    public $items;
    public $submissions;

    public function __construct()
    {
        $this->items        = [
            [
              'name'            => 'item-a',            # Nama Item
              'price'           => 70000,               # Harga Maximum
              'quantity'        => 1000,                # Jumlah item yang akan dikerjakan
              'production_time' => 8,                   # Lama pengerjaan dalam hari
              'start'           => '2017-11-14 10:00',  # Mulai bidding
              'end'             => '2017-11-14 12:00'   # Akhir bidding
            ],
            [
              'name'            => 'item-b',
              'price'           => 50000,
              'quantity'        => 2000,
              'production_time' => 10,
              'start'           => '2017-11-14 12:00',
              'end'             => '2017-11-14 15:00'
            ]
        ];
        $this->submissions = [
            [
              'name'    => 'Wili',                   # Nama Partner
              'bidding' => [
                'item-a' => [             # Submissions untuk item-a
                  '2017-11-14 10:00' => [    # Tanggal submit
                    'price'           => 65000,   # Harga yang ditawarkan
                    'production_time' => 9        # Lama pengerjaan dalam hari
                   ],
                  '2017-11-14 12:00' => [
                    'price'           => 68000,
                    'production_time' => 9
                   ],
                  '2017-11-14 10:30' => [
                    'price'           => 71000,
                    'production_time' => 9
                  ],
                  '2017-11-14 12:30' => [
                    'price'           => 10000,
                    'production_time' => 9
                  ]
                ],
          
                'item-b' => [
                  '2017-11-14 14:30' => [
                    'price'           => 40000,
                    'production_time' => 9
                  ],
                  '2017-11-14 12:30' => [
                    'price'           => 50000,
                    'production_time' => 9
                  ]
                ]
              ]
            ],
          
            [
              'name' => 'Lita',
              'bidding' => [
                'item-b' => [
                  '2017-11-14 13:30' => [
                    'price'           => 45000,
                    'production_time' => 9
                  ],
                  '2017-11-14 15:01' => [
                    'price'           => 35000,
                    'production_time' => 9
                  ],
                  '2017-11-14 12:30' => [
                    'price'           => 48000,
                    'production_time' => 9
                  ]
                ]
              ]
            ],
          
            [
              'name' => 'Sabar',
              'bidding' => [
                'item-a' => [
                  '2017-11-14 11:50' => [
                    'price'           => 65000,
                    'production_time' => 9
                  ],
                  '2017-11-14 11:30' => [
                    'price'           => 68000,
                    'production_time' => 9
                  ],
                  '2017-11-14 11:00' => [
                    'price'           => 69000,
                    'production_time' => 9
                  ]
                ]
              ]   
            ],
            [
              'name' => 'Makmur',
              'bidding' => [
                'item-a' => [
                  '2017-11-14 12:00' => [
                    'price'           => 50000,
                    'production_time' => 9
                  ],
                  '2017-11-14 11:00' => [
                    'price'           => 5000,
                    'production_time' => 9
                  ]
                ]
              ]
            ]
        ];
    }

    public function getSubmission($item, $price, $start, $end)
    {
        $submissions = $this->submissions;

        $result = [];
        foreach($submissions as $submit) :

            $user_bidding = [];
            foreach($submit['bidding'] as $name => $bidding){
                if($item == $name) :

                    foreach($bidding as $date => $bid) {
                        if(strtotime($date) >= strtotime($start) && strtotime($date) <= strtotime($end) && $bid['price'] <= $price) :
                            $user_bidding[] = [
                                'bidding_date'      => $date,
                                'price'             => $bid['price'],
                                'production_time'   => $bid['production_time'],
                            ];
                        endif;   
                    }
                endif;
            }
            if(!empty($user_bidding)) : 
                usort($user_bidding, function($a, $b){
                    return $b['bidding_date'] <=> $a['bidding_date'];
                });
                $result [] = [
                    'user'              => $submit['name'],
                    'bidding_date'      => $user_bidding[0]['bidding_date'],
                    'bidding_price'     => $user_bidding[0]['price'],
                    'production_time'   => $user_bidding[0]['production_time']
                ];
            endif;
        endforeach;
        return $result;
        
    }

    public function biddingResult()
    {
        $items = $this->items;

        foreach($items as $item) : 
            $result = $this->getSubmission($item['name'], $item['price'], $item['start'], $item['end']);

            usort($result, function($a, $b){
                return $a['bidding_price'] <=> $b['bidding_price'];
            });

            $item['count_user'] = count($result);
            $item['bidder']     = $result;

         $responses[] =  $item;
        endforeach;

        return $responses;
    }
}
?>
<?php 
    $bidding = new Bidding();
    $results = $bidding->biddingResult();
    // echo json_encode($results);

    $number = 1;
    foreach($results as $result) : 
        echo '<strong>'.$result['name'].' - '.$result['quantity'].' - '.$result['price'].'</strong><br>Peserta ('.$result['count_user'].'):';
        foreach($result['bidder'] as $key => $bidder) :
            echo '<table>
            <tr>
                <td width="20px">'.$number++.'</td>
                <td width="70px">'.$bidder['user'].'</td>
                <td width="120px">'.$bidder['bidding_date'].'</td>
                <td width="70px">'.$bidder['bidding_price'].'</td>
                <td width="100px">'.($bidder['bidding_price'] * $result['quantity']).'</td>
            </tr>
        </table>';
        endforeach; 
        echo '<br>';
        $number = 1;
    endforeach;
?>