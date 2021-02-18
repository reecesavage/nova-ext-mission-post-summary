$(document).ready(function() {
  

   var mission = $('[name="mission"]').val();
    if (typeof mission === "undefined") {
        mission = $('[name="post_mission"]').val();
    }

     getMission(mission);

     $(document).on("change", '[name="mission"]', function(e) {
        mission = $(this).val();
        getMission(mission);
    });

      $(document).on("change", '[name="post_mission"]', function(e) {
        mission = $(this).val();
        getMission(mission);
    });


       function getMission(mission) {

        $.ajax({
            type: "get",
            url: "<?php echo site_url('extensions/nova_ext_mission_post_summary/Ajax/mission')?>",
            data: {
                mission: mission
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status == 'OK') {

                   if(response.post.mission_ext_mission_post_summary_enable==1)
                   {
                     $('.nova_ext_mission_post_summary').css("display", "block");
                 }else {
                     $('.nova_ext_mission_post_summary').css("display", "none");
                 }

                }
            }
        });

    }
});