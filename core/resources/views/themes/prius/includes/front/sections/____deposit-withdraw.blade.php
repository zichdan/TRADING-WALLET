{{-- deposit and withdraw --}}
@foreach ($view_data['sections']->where('name', 'deposit_withdraw') as $section)
    <section class="transection-section section-bg padding-top padding-bottom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-10">
                    <div class="section__header max-p text-center">
                        <h2 class="section__header-title">{!! json_decode($section->content)->section_heading !!}</h2>
                        <div>
                            {!! json_decode($section->content)->section_text !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <ul class="transection__tab__menu nav-tabs nav border-0 justify-content-center">
                        <li><a data-bs-toggle="tab" href="#deposit" class="cmn--btn2 active">Last Deposit</a>
                        </li>
                        <li><a data-bs-toggle="tab" href="#widthdraw" class="cmn--btn2">Last Widthdraw</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show fade active" id="deposit">
                             <div class="container wow fadeInUp mt-5">
            <div class="row">
                <div class="col-md-12">

                    <!-- Tabs with icons on Card -->
                    <div class="card card-nav-tabs">
                        <div class="alert alert-success">
                            <h3><i class="fa fa-plus"></i> Latest Deposits</h3>
                        </div>

                        <div class="card-content">
                            <div class="tab-content text-center">
                                <div class="tab-pane active" id="deposit">

                                    <div class="table-responsive" style="height:400px; overflow-y:auto; overflow-x:auto;">
                                        <marquee direction="up" height="100%">
                                            <table
                                                    class="table">
                                                <thead> <tr> <th
                                                            class="text-center">Status</th> <th
                                                            class="text-center">Amount(USD)</th> <th
                                                            class="text-center">Wallet</th> </tr>
                                                </thead> <tbody> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center" style="color:blue;">$10,000.00</td> <td
                                                            class="text-center"style="color:blue;">3a17c5984af22cd7a443f14457841b3b19a51ea75a30e18bc6a82e4f8732dbca
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$24,100.00</td> <td
                                                            class="text-center"style="color:blue;">f007e92cc9f82ba9c8c40c481eec7315fa9abcd85e7249a6cb57e38a2cf22d3e</td>
                                                </tr> <tr> <td class="text-center"><button
                                                                class="btn btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$4,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$500</td> <td
                                                            class="text-center"style="color:blue;">00db85ef40da34f3ea76aa60f0b2053eec2d90121e450791c18d8edbfedde6f1
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$240,000</td> <td
                                                            class="text-center"style="color:blue;">b21a418a44ed8b56118facefe7aa8d26541dff811b8a8ca65cfa1346d62c5c48
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$17,000</td> <td
                                                            class="text-center"style="color:blue;">1e652d2899a1d058a20041a9faaeb5dc009101ca412ff09c387e6b281bd1db8b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">6a49e66a66f75e72e6bd383ac798792af204a6693708912ad0d48e363a2ab7a7
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$21,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$6,000</td> <td
                                                            class="text-center"style="color:blue;">797ba039291417fdbdb411ea0a102d23090cde9ac7799ff605f40b5db484891d
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">f0b66ce7a33bbc63bf50050beaf52be71709c359aa1d344bb90f943690485661
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$23,000</td> <td
                                                            class="text-center"style="color:blue;">2083e95ada3c584471ba5982e16c5dc2a6e464d3c170555ea8c708668be9383b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$5,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">5,000</td> <td
                                                            class="text-center"style="color:blue;">15c3a97edbd606bd1e455aa2875677f5c6cd2b804e9054e898f640d313e39781
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$18,000</td> <td
                                                            class="text-center"style="color:blue;">66c13496e9d53a2402fd49bbe91df298164472679cc868bbfebbabb4844d784c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$2,500</td> <td
                                                            class="text-center"style="color:blue;">ce972a6b82135fcba0890ea0c8668bdddf782fd580672daa6616c3b1af40ca9f
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">376e809b02e6ef044f6d5cf5b72111f25f3c3e16a93dce865a178e2e0f5c484c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$43,000</td> <td
                                                            class="text-center"style="color:blue;">aa14458f8082d9c4265ef491ca0b5d4801c16bbf7a4aece7b70a0b4824ffdfea
                                                    </td>
                                                </tr><tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$10,000.00</td> <td
                                                            class="text-center"style="color:blue;">3a17c5984af22cd7a443f14457841b3b19a51ea75a30e18bc6a82e4f8732dbca
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$24,100.00</td> <td
                                                            class="text-center"style="color:blue;">f007e92cc9f82ba9c8c40c481eec7315fa9abcd85e7249a6cb57e38a2cf22d3e</td>
                                                </tr> <tr> <td class="text-center"><button
                                                                class="btn btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$4,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$500</td> <td
                                                            class="text-center"style="color:blue;">00db85ef40da34f3ea76aa60f0b2053eec2d90121e450791c18d8edbfedde6f1
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$240,000</td> <td
                                                            class="text-center"style="color:blue;">b21a418a44ed8b56118facefe7aa8d26541dff811b8a8ca65cfa1346d62c5c48
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$17,000</td> <td
                                                            class="text-center"style="color:blue;">1e652d2899a1d058a20041a9faaeb5dc009101ca412ff09c387e6b281bd1db8b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">6a49e66a66f75e72e6bd383ac798792af204a6693708912ad0d48e363a2ab7a7
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$21,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$6,000</td> <td
                                                            class="text-center"style="color:blue;">797ba039291417fdbdb411ea0a102d23090cde9ac7799ff605f40b5db484891d
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">f0b66ce7a33bbc63bf50050beaf52be71709c359aa1d344bb90f943690485661
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$23,000</td> <td
                                                            class="text-center"style="color:blue;">2083e95ada3c584471ba5982e16c5dc2a6e464d3c170555ea8c708668be9383b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$5,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">5,000</td> <td
                                                            class="text-center"style="color:blue;">15c3a97edbd606bd1e455aa2875677f5c6cd2b804e9054e898f640d313e39781
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$18,000</td> <td
                                                            class="text-center"style="color:blue;">66c13496e9d53a2402fd49bbe91df298164472679cc868bbfebbabb4844d784c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$2,500</td> <td
                                                            class="text-center"style="color:blue;">ce972a6b82135fcba0890ea0c8668bdddf782fd580672daa6616c3b1af40ca9f
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">376e809b02e6ef044f6d5cf5b72111f25f3c3e16a93dce865a178e2e0f5c484c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$43,000</td> <td
                                                            class="text-center"style="color:blue;">aa14458f8082d9c4265ef491ca0b5d4801c16bbf7a4aece7b70a0b4824ffdfea
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </marquee>
                                    </div>




                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- End Tabs with icons on Card -->
                </div>
                        </div>
                        </div>
                        </div>
                        <div class="tab-pane show fade" id="widthdraw">
                             <div class="col-md-12">

                    <!-- Tabs with icons on Card -->
                    <div class="card card-nav-tabs">
                        <div class="alert alert-danger">
                            <h3><i class="fa fa-minus"></i> Latest Withdrawals</h3>
                        </div>
                        <div class="card-content">
                            <div class="tab-content text-center">
                                <div class="tab-pane active" id="deposit">

                                    <div class="table-responsive"style="height:400px; overflow-y:auto; overflow-x:auto;">
                                        <marquee direction="down" height="100%"> <table
                                                    class="table" style="width: 100%;">
                                                <thead> <tr> <th
                                                            class="text-center">Status</th> <th
                                                            class="text-center">Amount(USD)</th> <th
                                                            class="text-center">Wallet</th> </tr>
                                                </thead>
                                                <tbody>
                                                <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$10,000.00</td> <td
                                                            class="text-center"style="color:blue;">3a17c5984af22cd7a443f14457841b3b19a51ea75a30e18bc6a82e4f8732dbca
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$24,100.00</td> <td
                                                            class="text-center"style="color:blue;">f007e92cc9f82ba9c8c40c481eec7315fa9abcd85e7249a6cb57e38a2cf22d3e</td>
                                                </tr> <tr> <td class="text-center"><button
                                                                class="btn btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$4,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$500</td> <td
                                                            class="text-center"style="color:blue;">00db85ef40da34f3ea76aa60f0b2053eec2d90121e450791c18d8edbfedde6f1
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$240,000</td> <td
                                                            class="text-center"style="color:blue;">b21a418a44ed8b56118facefe7aa8d26541dff811b8a8ca65cfa1346d62c5c48
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$17,000</td> <td
                                                            class="text-center"style="color:blue;">1e652d2899a1d058a20041a9faaeb5dc009101ca412ff09c387e6b281bd1db8b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">6a49e66a66f75e72e6bd383ac798792af204a6693708912ad0d48e363a2ab7a7
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$21,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$6,000</td> <td
                                                            class="text-center"style="color:blue;">797ba039291417fdbdb411ea0a102d23090cde9ac7799ff605f40b5db484891d
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">f0b66ce7a33bbc63bf50050beaf52be71709c359aa1d344bb90f943690485661
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$23,000</td> <td
                                                            class="text-center"style="color:blue;">2083e95ada3c584471ba5982e16c5dc2a6e464d3c170555ea8c708668be9383b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$5,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">5,000</td> <td
                                                            class="text-center"style="color:blue;">15c3a97edbd606bd1e455aa2875677f5c6cd2b804e9054e898f640d313e39781
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$18,000</td> <td
                                                            class="text-center"style="color:blue;">66c13496e9d53a2402fd49bbe91df298164472679cc868bbfebbabb4844d784c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$2,500</td> <td
                                                            class="text-center"style="color:blue;">ce972a6b82135fcba0890ea0c8668bdddf782fd580672daa6616c3b1af40ca9f
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">376e809b02e6ef044f6d5cf5b72111f25f3c3e16a93dce865a178e2e0f5c484c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$43,000</td> <td
                                                            class="text-center"style="color:blue;">aa14458f8082d9c4265ef491ca0b5d4801c16bbf7a4aece7b70a0b4824ffdfea
                                                    </td>
                                                </tr><tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$10,000.00</td> <td
                                                            class="text-center"style="color:blue;">3a17c5984af22cd7a443f14457841b3b19a51ea75a30e18bc6a82e4f8732dbca
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$24,100.00</td> <td
                                                            class="text-center"style="color:blue;">f007e92cc9f82ba9c8c40c481eec7315fa9abcd85e7249a6cb57e38a2cf22d3e</td>
                                                </tr> <tr> <td class="text-center"><button
                                                                class="btn btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$4,000.00</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$500</td> <td
                                                            class="text-center"style="color:blue;">00db85ef40da34f3ea76aa60f0b2053eec2d90121e450791c18d8edbfedde6f1
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$240,000</td> <td
                                                            class="text-center"style="color:blue;">b21a418a44ed8b56118facefe7aa8d26541dff811b8a8ca65cfa1346d62c5c48
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$17,000</td> <td
                                                            class="text-center"style="color:blue;">1e652d2899a1d058a20041a9faaeb5dc009101ca412ff09c387e6b281bd1db8b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">6a49e66a66f75e72e6bd383ac798792af204a6693708912ad0d48e363a2ab7a7
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$21,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$6,000</td> <td
                                                            class="text-center"style="color:blue;">797ba039291417fdbdb411ea0a102d23090cde9ac7799ff605f40b5db484891d
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">f0b66ce7a33bbc63bf50050beaf52be71709c359aa1d344bb90f943690485661
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$23,000</td> <td
                                                            class="text-center"style="color:blue;">2083e95ada3c584471ba5982e16c5dc2a6e464d3c170555ea8c708668be9383b
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$51,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$5,000</td> <td
                                                            class="text-center"style="color:blue;">8a2b9781aa4995625af7d2b008f020ac74e7e0d2a599e93ed995f7c3bc2be9f2
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">5,000</td> <td
                                                            class="text-center"style="color:blue;">15c3a97edbd606bd1e455aa2875677f5c6cd2b804e9054e898f640d313e39781
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$18,000</td> <td
                                                            class="text-center"style="color:blue;">66c13496e9d53a2402fd49bbe91df298164472679cc868bbfebbabb4844d784c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$2,500</td> <td
                                                            class="text-center"style="color:blue;">ce972a6b82135fcba0890ea0c8668bdddf782fd580672daa6616c3b1af40ca9f
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn
                                    btn-success btn-sm"><span
                                                                    class="btn-label"><i class="fa
                                    fa-check"></i></span>Confirmed</button></td>
                                                    <td class="text-center"style="color:blue;">$9,000</td> <td
                                                            class="text-center"style="color:blue;">376e809b02e6ef044f6d5cf5b72111f25f3c3e16a93dce865a178e2e0f5c484c
                                                    </td> </tr> <tr> <td
                                                            class="text-center"><button class="btn btn-warning btn-sm"><span class="btn-label"><i class="fa fa-warning"></i></span>Pending</button></td>
                                                    <td class="text-center"style="color:blue;">$43,000</td> <td
                                                            class="text-center"style="color:blue;">aa14458f8082d9c4265ef491ca0b5d4801c16bbf7a4aece7b70a0b4824ffdfea
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </marquee>
                                    </div>




                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- End Tabs with icons on Card -->
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endforeach
