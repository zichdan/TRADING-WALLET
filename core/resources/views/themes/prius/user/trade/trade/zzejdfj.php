y<tr>
                                                        <td>
                                                            {{ date('H:i:s', $trade->trade_start) }} <br>
                                                            {{ date('d.m.Y', $trade->trade_start) }}
                                                        </td>
                                                        <td
                                                            @if ($trade->order_type == 'buy') class="crypt-up" @else class="crypt-down" @endif>
                                                            @if ($trade->order_type == 'buy')
                                                                <svg class="w-6 h-6" fill="none" width="15"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                                </svg>
                                                            @else
                                                                <svg class="w-6 h-6" fill="none" width="15"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                                </svg>
                                                            @endif

                                                            {{ ct($trade->order_type) }}
                                                        </td> 
                                                           <td>
                                                            {{ $trade->price }}</td>
                                                        <td>
                                                             <p id="cur-price">{{ $pair_info->close + $trade->coinz }}</p></td>
                                                        
                                                        <td>  <p class="@if (str()->contains(str_replace('%', '', $trade->leverage) * ((($pair_info->close + $trade->coinz) - $trade->price) * $trade->amount), '-')) crypt-down @else crypt-up @endif" id="las-price">
                                       {{ str_replace('%', '', $trade->leverage) * ((($pair_info->close + $trade->coinz) - $trade->price) * $trade->amount) }}</p></td>
                                       
                                                         <td>{{ $trade->leverage }}</td>
        
                                                        
                                                        
                                                    </tr>