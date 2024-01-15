jQuery(document).ready(function ($) {
    // Function to handle the vote button click
    $('.vote-button').on('click', function () {
        // Check if the user has already voted
        if ($(this).hasClass('user-vote')) {
            return;
        }

        // Get the vote type (positive or negative)
        var voteType = $(this).data('vote');

        // Send Ajax request to process the vote
        $.ajax({
            type: 'POST',
            url: post_voting_vars.ajax_url,
            data: {
                action: 'process_vote',
                vote_nonce: post_voting_vars.ajax_nonce,
                post_id: post_voting_vars.post_id,
                vote_type: voteType,
            },
            success: function (response) {
                if (response.success) {
                    // Update positive and negative counts
                    var positiveCount = response.data.positive_count;
                    var negativeCount = response.data.negative_count;

                    // Update the UI
                    $('.positive-num').text(positiveCount);
                    $('.negative-num').text(negativeCount);

                    // Update positive and negative percentages
                    var totalVotes = parseInt(positiveCount) + parseInt(negativeCount);
                    var positivePercentage = totalVotes > 0 ? (positiveCount / totalVotes) * 100 : 0;
                    var negativePercentage = totalVotes > 0 ? (negativeCount / totalVotes) * 100 : 0;

                    $('.positive-percentage').text(positivePercentage.toFixed(0) + '%');
                    $('.negative-percentage').text(negativePercentage.toFixed(0) + '%');

                    // Mark the button as voted and disable other buttons
                    $('.vote-button').removeClass('user-vote selected').prop('disabled', true);
                    $(`.vote-button[data-vote="${voteType}"]`).addClass('user-vote selected');
                } else {
                    // Handle errors if any
                    console.log('Error: ' + response.data.message);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                // Handle Ajax errors
                console.log('Error: ' + errorThrown);
            },
        });
    });

    // Function to check user vote status
    function checkUserVoteStatus() {
        $.ajax({
            type: 'POST',
            url: post_voting_vars.ajax_url,
            data: {
                action: 'check_user_vote_status',
                post_id: post_voting_vars.post_id,
            },
            success: function (response) {
                if (response.success && response.data.user_has_voted) {
                    // Mark the voted button and disable other buttons
                    $('.vote-button').removeClass('user-vote selected').prop('disabled', true);
                    $(`.vote-button[data-vote="${response.data.vote_type}"]`).addClass('user-vote selected');
                }
                
            },
            error: function (xhr, textStatus, errorThrown) {
                // Handle Ajax errors
                console.log('Error: ' + errorThrown);
            },
        });
    }

    // Call the function to check user vote status on page load
    checkUserVoteStatus();
});