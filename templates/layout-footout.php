  <?php if ($page == 'signup') { ?>
    <script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.12.1.custom.min.js"></script>
    <script type="text/javascript" src="js/selectize.min.js"></script>

    <?php
    $yearStart = 1900;
    $yearEnd = date('Y') + 100;
    $yearCurrent = date('Y');
    $yearRange = range($yearCurrent, $yearStart);
    ?>

    <script type="text/javascript">
      jQuery(document).ready(function () {

        // // Submit form only once
        // $('form.submit-once').on('submit', function(e){
        //   if( $(this).hasClass('form-submitted') ){
        //     e.preventDefault();
        //     return;
        //   }
        //   $(this).addClass('form-submitted');
        // });

        $(".gaw-signup .button.join-now").on("click",function(){
            $("#register-form").css("display","block");
            $("#features-overview").css("display","none");
            $('html,body').animate({
                scrollTop: $("#register-form").offset().top
            }, 'slow');
        });
        $(".gaw-signup .button.show-features").on("click",function(){
            $("#register-form").css("display","none");
            $("#features-overview").css("display","block");
            $('html,body').animate({
                scrollTop: $("#features-overview").offset().top
            }, 'slow');
        });

        if( $('#teachLocationName').length ) {
          $('#teachLocationName').selectize({
            delimiter: ',',
            persist: false,
            maxOptions: 50,
            preload: true,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '<div class="dropdown-subtitle">' + escape(item.city) + ', ' + escape(item.state) + '</div></div>';
              }
            },
            onItemAdd: function(value, $item) {
              // console.log(this.options[value]);
              var city = this.options[value]['city'];
              var state = this.options[value]['state'];
              if (city) document.getElementById('teachLocationCity').value=city;
              if (state) document.getElementById('teachLocationState').value=state;
            },
            onItemRemove: function(value) {
              document.getElementById('teachLocationCity').value='';
              document.getElementById('teachLocationState').value='';
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '<?php site_url();?>/schools.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'terms': query,
                        'limit': 50,
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
        								callback(result);
      							}
                });
            },
            create: function(input) {
                return {name: input}
            }
          });
        }

        if( $('#teachGrades').length ) {
          $('#teachGrades').selectize({
            delimiter: ',',
            persist: false,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              <?php include('grades.php'); ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: false
          });
        }

        if( $('#teachSubjects').length ) {
          $('#teachSubjects').selectize({
            delimiter: ',',
            persist: false,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              <?php include('subjects.php'); ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: function(input) {
                return {name: input}
            }
          });
        }

        if( $('#teachStart').length ) {
          $('#teachStart').selectize({
            delimiter: ',',
            persist: false,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              <?php
              for ($count = $yearCurrent; $count >= $yearStart; $count--) {
                  echo "{name: '{$count}'},";
              }
              ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: false
          });
        }

        if( $('#teachEnd').length ) {
          $('#teachEnd').selectize({
            delimiter: ',',
            persist: false,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              {name: 'Present'},
              <?php
              for ($count = $yearCurrent; $count >= $yearStart; $count--) {
                  echo "{name: '{$count}'},";
              }
              ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: false
          });
          <?php if ($userType !== 'other' || $userType !== 'invite') { ?>
            var $select = $('#teachEnd').selectize();
            $select[0].selectize.setValue("Present");
          <?php } ?>
        }

        if( $('#teachLicenseLocation').length ) {
          $('#teachLicenseLocation').selectize({
            delimiter: ',',
            persist: false,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              <?php include('institutes.php'); ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: function(input) {
                return {name: input}
            }
          });
        }

        if( $('#teachLicenseComplete').length ) {
          $('#teachLicenseComplete').selectize({
            delimiter: ',',
            persist: false,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: 'name',
            selectOnTab: true,
            options: [
              <?php if ($userType == 'student') {
                for ($count = $yearCurrent; $count <= $yearEnd; $count++) {
                    echo "{name: '{$count}'},";
                }
              } else {
                for ($count = $yearCurrent; $count >= $yearStart; $count--) {
                    echo "{name: '{$count}'},";
                }
              } ?>
            ],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title">' + escape(item.name) + '</div>';
              }
            },
            create: false
          });
        }


          $('#accordion-form').accordion({
            heightStyle: "content",
            activate: function( event, ui ) {
              var active = $('#accordion-form').accordion('option', 'active');
                $('.supplement').fadeOut();
                $('.supplement.text'+active).fadeIn();
              <?php if ($userType == 'student') { ?>
                if (active == '1') {
              <?php } else { ?>
                if (active == '2') {
              <?php } ?>
                $('#tab-license').addClass('corners-square');
              } else {
                $('#tab-license').removeClass('corners-square');
              }
            }
          });

          // $('#accordion-form').on('click', 'button', function(e) {
          $('#accordion-form button').click(function (e) {
              if ( ($('#user').val() && $('#user-check').val() && $('#pass').val() && $('#pass-check').val()) && ($('#user').val() == $('#user-check').val()) && ($('#pass').val() == $('#pass-check').val()) ) {
                e.preventDefault();
                $('#tab-identity').removeClass('error');
                $('#user-check').parent().removeClass('error');
                $('#pass-check').parent().removeClass('error');
                var delta = ($(this).is('.next') ? 1 : -1);
                $('#accordion-form').accordion('option', 'active', ($('#accordion-form').accordion('option', 'active') + delta));
              } else {
                e.preventDefault();
                $('#tab-identity').addClass('error');
                $('#accordion-form').accordion('option', 'active', 0);
                if ( ($('#user').val() != $('#user-check').val()) || (!$('#user').val()) || (!$('#user-check').val())) {
                  $('#user-check').parent().addClass('error');
                } else {
                  $('#user-check').parent().removeClass('error');
                }
                if ( ($('#pass').val() != $('#pass-check').val()) || (!$('#pass').val()) || (!$('#pass-check').val())) {
                  $('#pass-check').parent().addClass('error');
                } else {
                  $('#pass-check').parent().removeClass('error');
                }
                
                if ( (!$('#firstName').val()) ) {
                  $('#firstName').parent().addClass('error');
                } else {
                  $.ajax({
                    url: '<?php site_url();?>/username-validation.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'username': $('#firstName').val()
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                     if(result == 1){
                        $('#firstName').parent().removeClass('error');
                        $('#fail_firstname').text('');
                     }else if(result == 2){
                        $('#firstName').parent().addClass('error');
                        $('#fail_firstname').text('2-25 characters, must start with a letter');
                     }else{
                        $('#firstName').parent().removeClass('error');
                     }
      							}
                  });    
                }

                if ( (!$('#lastName').val()) ) {
                  $('#lastName').parent().addClass('error');
                }else {
                   $.ajax({
                    url: '<?php site_url();?>/username-validation.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'username': $('#lastName').val()
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                     if(result == 1){
                        $('#lastName').parent().removeClass('error');
                        $('#fail_lastname').text('');
                     }else if(result == 2){
                        $('#lastName').parent().addClass('error');
                        $('#fail_lastname').text('2-25 characters, must start with a letter');
                     }else{
                        $('#lastName').parent().removeClass('error');
                     }
      							}
                  }); 
                }
              }
          });

          $('#accordion-form input[type="submit"]').click(function (e) {
            if ( ($('#user').val() && $('#user-check').val() && $('#pass').val() && $('#pass-check').val()) && ($('#user').val() == $('#user-check').val()) && ($('#pass').val() == $('#pass-check').val()) ) {
              // e.preventDefault();                
              
              $('#tab-identity').removeClass('error');
              $('#user-check').parent().removeClass('error');
              $('#pass-check').parent().removeClass('error');
              // var delta = ($(this).is('.next') ? 1 : -1);
              // $('#accordion-form').accordion('option', 'active', ($('#accordion-form').accordion('option', 'active') + delta));
            } else {
              e.preventDefault();
              $('#tab-identity').addClass('error');
              $('#accordion-form').accordion('option', 'active', 0);
              if ( ($('#user').val() != $('#user-check').val()) || (!$('#user').val()) || (!$('#user-check').val())) {
                $('#user-check').parent().addClass('error');
              } else {
                $('#user-check').parent().removeClass('error');
              }
              if ( ($('#pass').val() != $('#pass-check').val()) || (!$('#pass').val()) || (!$('#pass-check').val())) {
                $('#pass-check').parent().addClass('error');
              } else {
                $('#pass-check').parent().removeClass('error');
              }
              
              if ( (!$('#firstName').val()) ) {
                  $('#firstName').parent().addClass('error');
              }  else {
                $.ajax({
                    url: '<?php site_url();?>/username-validation.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'username': $('#firstName').val()
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                     if(result == 1){
                        $('#firstName').parent().removeClass('error');
                        $('#fail_firstname').text('');
                     }else if(result == 2){
                        $('#firstName').parent().addClass('error');
                        $('#fail_firstname').text('2-25 characters, must start with a letter');
                     }else{
                        $('#firstName').parent().removeClass('error');
                     }
      							}
                });                
              }

              if ( (!$('#lastName').val()) ) {
                $('#lastName').parent().addClass('error');
              } else {
                $.ajax({
                    url: '<?php site_url();?>/username-validation.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'username': $('#lastName').val()
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                     if(result == 1){
                        $('#lastName').parent().removeClass('error');
                        $('#fail_lastname').text('');
                     }else if(result == 2){
                        $('#lastName').parent().addClass('error');
                        $('#fail_lastname').text('2-25 characters, must start with a letter');
                     }else{
                        $('#lastName').parent().removeClass('error');
                     }
      							}
                });                
              }
            }
          });

      });</script>
  <?php } ?>
  </body>
</html>
