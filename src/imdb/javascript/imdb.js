function getMovie()
{
    showPrompt( "Find a Movie", "Enter a movie title:", displayMovie, "Casablanca" );
}

function displayMovie( response )
{
    if ( response )
    {
        $.post(
            "php/imdbGet.php",
            {title: response},
            displayMovieCallback
        );
    }
}

function displayMovieCallback( response )
{
    var movieResponse = JSON.parse( response );
    if ( movieResponse.search )
    {
        var html = "No movie was found. Try removing articles or conjunctions.<br />" +
                   "Or go here: <a class='link' href='https://www.google.com/search?q=IMDB%20" + movieResponse.search + "'>Google</a>";
        if ( movieResponse.isSuccess )
        {
            html = "<strong>" + movieResponse.title + "</strong> (" + movieResponse.year + ")<br />" +
                   "<strong>ID:</strong> " + movieResponse.id + "<br /><br />" +
                   "<img src='" + movieResponse.poster + "' height='300px' alt='Movie Poster'>";
        }
        showMessage( "Movie Match", html );
    }
}

function uploadFile()
{
    $('#file').one( "change", uploadFileCallback );
    $('#file').click();
}

function uploadFileCallback()
{
    var file = $("#file")[0].files[0];

    if ( isSafe( file ) )
    {
        displayInfo( "Converting <strong>" + file.name + "</strong>...<br/>Allow up to 90 seconds." );

        var data = new FormData();
        data.append( "file", file, file.name );

        $.ajax({
            url: "php/imdbConvert.php",
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            timeout: 90000,
            error: function( e ) { displayInfo( "An error has occurred.", true ) },
            success: function( response ) {
                response = JSON.parse( response );
                if ( response.isSuccess && response.contents )
                {
                    downloadFile( response.contents );
                    displayRiskTitles( response.message );
                }
                else
                {
                    displayInfo( response.message, true );
                }
            }
        });
    }
}

function downloadFile( text )
{
    var url = null;
    var blob = new Blob( [text], {type: 'text/csv'} );
    if ( url !== null )
    {
        window.URL.revokeObjectURL( url );
    }
    url = window.URL.createObjectURL( blob );

    var a = document.createElement( "a" );
    document.body.appendChild( a );
    a.href = url;
    a.style = "display: none";
    a.download = "UpdatedMovies.csv";
    a.click();
    window.URL.revokeObjectURL( url );
}

function displayRiskTitles( message )
{
    var riskTitleMessage = "";
    var riskMovies = [];
    for ( var i = 0; i < message.length; i++ )
    {
        if ( message[i] && message[i].index )
        {
            var movie = message[i];
            riskMovies.push( movie );
            riskTitleMessage += movie.index + ". " + movie.title + " | <span id='span" + movie.index + "' class='link'>" + (movie.newTitle || "No Movie Found") + "</span><br/>";
        }
        else if ( message[i] )
        {
            riskTitleMessage += message[i] + "<br/>";
        }
    }

    if ( riskTitleMessage )
    {
        displayInfo( "<strong>The following movies may have matched incorrectly:</strong><br/><br/>" + riskTitleMessage );
        $.each( riskMovies, function( index, movie ) {
            $( '#span' + movie.index ).click( function() {
                if ( movie.newTitle )
                {
                    var imageHTML = movie.poster ? ("<img src='" + movie.poster + "' height='300px' alt='Movie Poster'>") : "";
                    showMessage( "Movie ID", movie.id + "<br />" + imageHTML );
                }
                else
                {
                    var innerHTML = "Error: " + movie.id + "<br/>" +
                                    "Try searching <a class='link' href='https://www.google.com/search?q=IMDB%20" + movie.title + "'>Google</a>.";
                    showMessage( "Error", innerHTML );
                }
            } );
        } );
    }
    else
    {
        displayInfo( "Finished converting." );
    }
}

function isSafe( file )
{
    var isSafe = true;
    if ( !file ) {
        isSafe = false;
    }
    if ( isSafe && file.size > 250000 ) {
        displayInfo( "Sorry, your file is too large.", true );
        isSafe = false;
    }
    if ( isSafe && file.name.split('.').pop().toLowerCase() !== "csv" ) {
        displayInfo( "Sorry, only CSV files are allowed.", true );
        isSafe = false;
    }
    return isSafe;
}

function displayInfo( message, isError )
{
    $('#info').html( message );
    if ( isError )
    {
        showToaster( message );
    }
}