<section id="intro" class="container" style='background-image: -moz-linear-gradient(45deg, #361752, #B31B67);color:#fff;padding:40px;margin-top:0px;margin-bottom:20px;'>
    <div class="row">
        <div class="col-md-12">
            <h1 property="name" class='previewTitle eventTxt col-xs-12 col-md-8' style='margin-left:0px;padding-left:0px;max-width:80%;font-size:28px;'>Sample Event w/ Special Guests</h1>
            <h3 class='col-xs-12 col-md-8'> The event sub-title and more...! </h3>
            <div class="event_venue" style='min-height:120px;'>
                <figure style='max-width:20%;width:128px;transform:scale(0.5);position:absolute;top:-20px;right:-20px;text-align:center;'>
                    <header style='font-size:22px;font-weight:400;'>Monday</header><br />
                    <section>21</section><br />
                    <span style='color:#fff;font-size:30px;width:128px;text-align:center;'> Jan </span>
                    <span style='color:#fff;font-size:28px;width:128px;'> 18:33 PM <span style='font-size:18px;'>(UTC+6)</span>
                </figure>                                                
            </div>

            <div class="event_buttons" style='margin-top:40px;clear:both;'>
                <div class="row">
                    <div class="col-md-4 col-sm-4" style='border:1px solid #fff3;text-align:center;background-color:#fff1;'>
                        <a class="btn btn-event-link btn-lg eventTxt" href="#tickets" style='color:#fff;'>TICKETS</a>
                    </div>
                    <div class="col-md-4 col-sm-4" style='border:1px solid #fff3;text-align:center;background-color:#fff1;'>
                        <a class="btn btn-event-link btn-lg eventTxt" href="#details" style='color:#fff;'>DETAILS</a>
                    </div>
                    <div class="col-md-4 col-sm-4" style='border:1px solid #fff3;text-align:center;background-color:#fff1;'>
                        <a class="btn btn-event-link btn-lg eventTxt" href="#details" style='color:#fff;'>LIVE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="tickets" class="container" style='background-color:#d5a7ff1a;border-radius:3px;'>
    <div class="row">
        <h1 class="section_head eventTxt" style='font-size:16px !important;'>
            Tickets
        </h1>
    </div>

    <form method="POST" action="http://attendize.website/e/799/checkout" accept-charset="UTF-8" class="ajax"><input name="_token" value="QdzfHNFCKk0r8zEUjO2tayqfhuefrrqxqRUPobI4" type="hidden">
        <div class="row">
            <div class="col-md-12">
                <div class="content">
                    <div class="tickets_table_wrap">
                        <table class="table">
                            <tbody>
                                <tr class="ticket" property="offers" typeof="Offer">
                                    <td>
                                        <span class="ticket-title semibold" property="name">
                                            Free Ticket
                                        </span>
                                        <p class="ticket-descripton mb0 text-muted" property="description">
                                            Sample description
                                        </p>
                                    </td>
                                    <td style="width:180px; text-align: right;">
                                        <div class="ticket-pricing" style="margin-right: 20px;">
                                            FREE
                                            <meta property="price" content="0">
                                        </div>
                                    </td>
                                    <td style="width:85px;">

                                         <input name="tickets[]" value="909" type="hidden">
                                         <meta property="availability" content="http://schema.org/InStock">
                                         <select name="ticket_909" class="form-control" style="text-align: center">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                        </select>

                                    </td>
                                </tr>
                                <tr class="ticket" property="offers" typeof="Offer">
                                    <td>
                                        <span class="ticket-title semibold" property="name">
                                            General Ticket
                                        </span>
                                        <p class="ticket-descripton mb0 text-muted" property="description">
                                            Sample description
                                        </p>
                                    </td>
                                    <td style="width:180px; text-align: right;">
                                        <div class="ticket-pricing" style="margin-right: 20px;">
                                            <span title="€10.00 Ticket Price + €0.00 Booking Fees">€10.00 </span>
                                            <meta property="priceCurrency" content="EUR">
                                            <meta property="price" content="10.00">
                                        </div>
                                    </td>
                                    <td style="width:85px;">

                                     <input name="tickets[]" value="908" type="hidden">
                                     <meta property="availability" content="http://schema.org/InStock">
                                     <select name="ticket_908" class="form-control" style="text-align: center">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                    </select>
                                    
                                    </td>
                                </tr>                        
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input name="is_embedded" value="0" type="hidden">
    </form>
</section>

<section id="organiserHead" style='margin-top:15px;'>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class='col-xs-4' style='text-align:center;'>
                    <span class='eventTxt' style='font-size:12px;'>Status <span class="label label-success eventTxt">ACTIVE</span>
                </div>
                <div onclick="window.location='channel'" class="event_organizer col-xs-4" style='text-align:center;font-size:18px;'>
                    <b class='eventTxt'>Organiser</b> <?php echo User::getName(); ?>
                    <img src='<?php echo User::getPhoto(); ?>' style='display:inline;width:45px;height:45px;border-radius:15px;margin-top:-5px;' />
                </div>
                <div class="btn-group col-xs-4" style='text-align:center;'>
                    <div style='margin:15px auto 0px; text-align:center;'>
                        <button class="btn btn-xs youmake-button subsB subs1 subscribeButton1" style="height:35px;line-height:35px;">
                            <i class="fab fa-youtube"></i> <b class="text">Subscribe</b>
                        </button>
                        <!--
                        <button class="btn btn-xs youmake-button subsB subs1" style="height:35px;line-height:35px;">
                            <b class="textTotal1">0</b>
                        </button>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="details" class="container">
    <div class="row">
        <h1 class="section_head eventTxt">
            Event Details
        </h1>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="content event_details previewDescription" property="description" style='text-align:justify;'>
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br /><br />
                
                <b>Why do we use it?</b>
                <br />It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).<br /><br />

                <b>Where does it come from?</b>
                <br />Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.<br /><br />

                <br />The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanie<br />
            </div>
        </div>
        <div class="col-md-5">
            <div class="content event_poster">
                <img alt="" src="<?php echo $global['webSiteRootURL']; ?>/view/img/blur-camera.jpg" property="image" style='width:150px;min-height:150px;margin-top:30px;'>
            </div>
        </div>
    </div>
</section>

<section class="container" id="sponsors">
    <div class="row">
        <h1 class="section_head eventTxt">
            Sponsors
        </h1>
    </div>
    <div>
        
    </div>
</section>

<section id="liveContainer" class="container">
    <div class="row">
        <h1 class="section_head eventTxt">
            Live streaming
        </h1>
    </div>
    <div class='clock-div eventTxt'>
        <label> Starts in: </label>
        <div id="clock"></div>
    </div>
    <div class="container-fluid principalContainer " itemscope itemtype="http://schema.org/VideoObject" style='margin-top:10px;'>
        <div class="col-md-12">
            <?php require "{$global['systemRootPath']}plugin/Live/view/liveVideo.php"; ?>
        </div>  
    </div>
</section> 

<section class="container" id="attendees">
    <div class="row">
        <h1 class="section_head eventTxt">
            Attendees
        </h1>
    </div>
    <div class="photo-list">
        <ul>
            <li class="circle">
                <img data-id="539" data-uses="1842" alt="leo gill" data-tooltip-content="#info-539" src="https://randomuser.me/api/portraits/men/32.jpg" data-original="https://randomuser.me/api/portraits/men/32.jpg" class="lazyload initial tooltipstered loaded" data-was-processed="true">
                <span class="info-tooltip"></span>
            </li>
            <li class="circle">
                <img data-id="965" data-uses="1431" alt="Miyah Myles" data-tooltip-content="#info-965" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-0.3.5&amp;q=80&amp;fm=jpg&amp;crop=entropy&amp;cs=tinysrgb&amp;w=200&amp;fit=max&amp;s=707b9c33066bf8808c934c8ab394dff6" data-original="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-0.3.5&amp;q=80&amp;fm=jpg&amp;crop=entropy&amp;cs=tinysrgb&amp;w=200&amp;fit=max&amp;s=707b9c33066bf8808c934c8ab394dff6" class="lazyload initial tooltipstered loaded" data-was-processed="true">
                <span class="info-tooltip"></span>
                <label class='success'> Fan </label>
            </li>
            <li class="circle">
                <img data-id="864" data-uses="40" alt="Tamar Heard" data-tooltip-content="#info-864" src="https://images.pexels.com/photos/308249/pexels-photo-308249.jpeg?h=350&amp;auto=compress&amp;cs=tinysrgb" data-original="https://images.pexels.com/photos/308249/pexels-photo-308249.jpeg?h=350&amp;auto=compress&amp;cs=tinysrgb" class="lazyload tooltipstered loaded" data-was-processed="true">
                <span class="info-tooltip"></span>
                <label class='success'> Promoter </label>
            </li>
        </ul>
    </div>
</section>

<section id="share" class="container">
    <div class="row">
        <h1 class="section_head eventTxt">
            Share Event
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul class="rrssb-buttons clearfix rrssb-1">

                <li class="rrssb-facebook col-xs-6 col-sm-3 col-md-2" data-size="69">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=" class="popup">
                        <span class="rrssb-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                                <path d="M27.825,4.783c0-2.427-2.182-4.608-4.608-4.608H4.783c-2.422,0-4.608,2.182-4.608,4.608v18.434
                                c0,2.427,2.181,4.608,4.608,4.608H14V17.379h-3.379v-4.608H14v-1.795c0-3.089,2.335-5.885,5.192-5.885h3.718v4.608h-3.726
                                c-0.408,0-0.884,0.492-0.884,1.236v1.836h4.609v4.608h-4.609v10.446h4.916c2.422,0,4.608-2.188,4.608-4.608V4.783z"></path>
                            </svg>
                        </span>
                        <span class="rrssb-text">facebook</span>
                    </a>
                </li>
                <li class="rrssb-linkedin col-xs-6 col-sm-3 col-md-2" data-size="57">
                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=" class="popup">
                        <span class="rrssb-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                                <path d="M25.424,15.887v8.447h-4.896v-7.882c0-1.979-0.709-3.331-2.48-3.331c-1.354,0-2.158,0.911-2.514,1.803
                                c-0.129,0.315-0.162,0.753-0.162,1.194v8.216h-4.899c0,0,0.066-13.349,0-14.731h4.899v2.088c-0.01,0.016-0.023,0.032-0.033,0.048
                                h0.033V11.69c0.65-1.002,1.812-2.435,4.414-2.435C23.008,9.254,25.424,11.361,25.424,15.887z M5.348,2.501
                                c-1.676,0-2.772,1.092-2.772,2.539c0,1.421,1.066,2.538,2.717,2.546h0.032c1.709,0,2.771-1.132,2.771-2.546
                                C8.054,3.593,7.019,2.501,5.343,2.501H5.348z M2.867,24.334h4.897V9.603H2.867V24.334z"></path>
                            </svg>
                        </span>
                        <span class="rrssb-text">linkedin</span>
                    </a>
                </li>
                <li class="rrssb-twitter col-xs-6 col-sm-3 col-md-2" data-size="52">
                    <a href="http://twitter.com/intent/tweet?text=" class="popup">
                        <span class="rrssb-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                                <path d="M24.253,8.756C24.689,17.08,18.297,24.182,9.97,24.62c-3.122,0.162-6.219-0.646-8.861-2.32
                                c2.703,0.179,5.376-0.648,7.508-2.321c-2.072-0.247-3.818-1.661-4.489-3.638c0.801,0.128,1.62,0.076,2.399-0.155
                                C4.045,15.72,2.215,13.6,2.115,11.077c0.688,0.275,1.426,0.407,2.168,0.386c-2.135-1.65-2.729-4.621-1.394-6.965
                                C5.575,7.816,9.54,9.84,13.803,10.071c-0.842-2.739,0.694-5.64,3.434-6.482c2.018-0.623,4.212,0.044,5.546,1.683
                                c1.186-0.213,2.318-0.662,3.329-1.317c-0.385,1.256-1.247,2.312-2.399,2.942c1.048-0.106,2.069-0.394,3.019-0.851
                                C26.275,7.229,25.39,8.196,24.253,8.756z"></path>
                            </svg>
                        </span>
                        <span class="rrssb-text">twitter</span>
                    </a>
                </li>
                <li class="rrssb-googleplus col-xs-6 col-sm-3 col-md-2" data-size="58">
                    <a href="https://plus.google.com/share?url=" class="popup">
                        <span class="rrssb-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M14.703,15.854l-1.219-0.948c-0.372-0.308-0.88-0.715-0.88-1.459c0-0.748,0.508-1.223,0.95-1.663
                                        c1.42-1.119,2.839-2.309,2.839-4.817c0-2.58-1.621-3.937-2.399-4.581h2.097l2.202-1.383h-6.67c-1.83,0-4.467,0.433-6.398,2.027
                                        C3.768,4.287,3.059,6.018,3.059,7.576c0,2.634,2.022,5.328,5.604,5.328c0.339,0,0.71-0.033,1.083-0.068
                                        c-0.167,0.408-0.336,0.748-0.336,1.324c0,1.04,0.551,1.685,1.011,2.297c-1.524,0.104-4.37,0.273-6.467,1.562
                                        c-1.998,1.188-2.605,2.916-2.605,4.137c0,2.512,2.358,4.84,7.289,4.84c5.822,0,8.904-3.223,8.904-6.41
                                        c0.008-2.327-1.359-3.489-2.829-4.731H14.703z M10.269,11.951c-2.912,0-4.231-3.765-4.231-6.037c0-0.884,0.168-1.797,0.744-2.511
                                        c0.543-0.679,1.489-1.12,2.372-1.12c2.807,0,4.256,3.798,4.256,6.242c0,0.612-0.067,1.694-0.845,2.478
                                        c-0.537,0.55-1.438,0.948-2.295,0.951V11.951z M10.302,25.609c-3.621,0-5.957-1.732-5.957-4.142c0-2.408,2.165-3.223,2.911-3.492
                                        c1.421-0.479,3.25-0.545,3.555-0.545c0.338,0,0.52,0,0.766,0.034c2.574,1.838,3.706,2.757,3.706,4.479
                                        c-0.002,2.073-1.736,3.665-4.982,3.649L10.302,25.609z"></path>
                                        <polygon points="23.254,11.89 23.254,8.521 21.569,8.521 21.569,11.89 18.202,11.89 18.202,13.604 21.569,13.604 21.569,17.004
                                        23.254,17.004 23.254,13.604 26.653,13.604 26.653,11.89"></polygon>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="rrssb-text">google+</span>
                    </a>
                </li>
                <li class="rrssb-whatsapp col-xs-6 col-sm-3 col-md-2" data-size="58">
                 <a style="background-color: #43d854;" href="whatsapp://send?text=" data-action="share/whatsapp/share">
                    <span class="rrssb-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="28px" height="28px" viewBox="0 0 90 90"><path d="M90 43.84c0 24.214-19.78 43.842-44.182 43.842a44.256 44.256 0 0 1-21.357-5.455L0 90l7.975-23.522a43.38 43.38 0 0 1-6.34-22.637C1.635 19.63 21.415 0 45.818 0 70.223 0 90 19.628 90 43.84zM45.818 6.983c-20.484 0-37.146 16.535-37.146 36.86 0 8.064 2.63 15.533 7.076 21.61l-4.64 13.688 14.274-4.537A37.122 37.122 0 0 0 45.82 80.7c20.48 0 37.145-16.533 37.145-36.857S66.3 6.983 45.818 6.983zm22.31 46.956c-.272-.447-.993-.717-2.075-1.254-1.084-.537-6.41-3.138-7.4-3.495-.993-.36-1.717-.54-2.438.536-.72 1.076-2.797 3.495-3.43 4.212-.632.72-1.263.81-2.347.27-1.082-.536-4.57-1.672-8.708-5.332-3.22-2.848-5.393-6.364-6.025-7.44-.63-1.076-.066-1.657.475-2.192.488-.482 1.084-1.255 1.625-1.882.543-.628.723-1.075 1.082-1.793.363-.718.182-1.345-.09-1.884-.27-.537-2.438-5.825-3.34-7.977-.902-2.15-1.803-1.793-2.436-1.793-.63 0-1.353-.09-2.075-.09-.722 0-1.896.27-2.89 1.344-.99 1.077-3.788 3.677-3.788 8.964 0 5.288 3.88 10.397 4.422 11.113.54.716 7.49 11.92 18.5 16.223 11.01 4.3 11.01 2.866 12.996 2.686 1.984-.18 6.406-2.6 7.312-5.107.9-2.513.9-4.664.63-5.112z"></path></svg>
                  </span>
                  <span class="rrssb-text">Whatsapp</span>
                  </a>
                </li>
                <li class="rrssb-email col-xs-6 col-sm-3 col-md-2" data-size="37">
                    <a href="mailto:?subject=">
                        <span class="rrssb-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve"><g><path d="M20.111 26.147c-2.336 1.051-4.361 1.401-7.125 1.401c-6.462 0-12.146-4.633-12.146-12.265 c0-7.94 5.762-14.833 14.561-14.833c6.853 0 11.8 4.7 11.8 11.252c0 5.684-3.194 9.265-7.399 9.3 c-1.829 0-3.153-0.934-3.347-2.997h-0.077c-1.208 1.986-2.96 2.997-5.023 2.997c-2.532 0-4.361-1.868-4.361-5.062 c0-4.749 3.504-9.071 9.111-9.071c1.713 0 3.7 0.4 4.6 0.973l-1.169 7.203c-0.388 2.298-0.116 3.3 1 3.4 c1.673 0 3.773-2.102 3.773-6.58c0-5.061-3.27-8.994-9.303-8.994c-5.957 0-11.175 4.673-11.175 12.1 c0 6.5 4.2 10.2 10 10.201c1.986 0 4.089-0.43 5.646-1.245L20.111 26.147z M16.646 10.1 c-0.311-0.078-0.701-0.155-1.207-0.155c-2.571 0-4.595 2.53-4.595 5.529c0 1.5 0.7 2.4 1.9 2.4 c1.441 0 2.959-1.828 3.311-4.087L16.646 10.068z"></path></g></svg>
                        </span>
                        <span class="rrssb-text">email</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</section>

<section id="pastlive" class="container">
    <div class='row'>
        <h1 class='section_head eventTxt'>
            Pasts Live events
        </h1>
        <div class='row'>

        </div>
</section>

<section>
    <div class="row">
        <h1 class="section_head eventTxt">
            Comments
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12" style='padding:30px;'>
            <?php include $global['systemRootPath'] . 'view/videoComments.php'; ?>
        </div>
    </div>
</section>