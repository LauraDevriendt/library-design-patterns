<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'resources/mainClasses.php';
require 'resources/Patterns/factoryFormat.php';
require 'resources/Patterns/compositePages.php';
require 'resources/Patterns/stateLibraryFlow.php';

session_start();

/* OUTPUT FROM FACTORY FORMAT */
$books = clientCode(new BookImporterCsv('resources/books.csv'));

/* Establishing Library */
if(isset($_SESSION['library'])){
    $library=$_SESSION['library'];
}else{
    $library = new Library();
    $library->setBooks($books);
    $_SESSION['library']=$library;
}
/* CONCERNING COMPOSITE */
switch(isset($_POST))
{
case isset($_POST['bookSearchInput']):
    $searchInput = new PartialBookSearch($_POST['bookSearchInput']);
    break;

case isset($_POST['genre'])&& $_POST['genre']!=='':
    $searchInput = new Genre($_POST['genre']);
    break;

case isset($_POST['publisher'])&& $_POST['publisher']!=='':
    $searchInput = new Publisher($_POST['publisher']);

}
if($_SERVER['REQUEST_METHOD']=='POST'){

    $searchMatches = $searchInput->SearchMatch($library, $searchInput->getSearchInput());
}

/* OUTPUT FROM state FORMAT */
if(isset($_GET['state'])){
    $book = Library::searchBook($library,$_GET['title']);
    $context= $book->getContext();
    switch ($_GET['state']){
        case 'lended':
            $context->borrow();
            break;
        case 'sold':
            $context->buy();
            break;
        case 'lost':
            $context->lost();
            break;
        case 'open':
            $context->open();
            break;

    }
    $book->setContext($context);

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>Library</title>
</head>
<body>
<h1 class="text-center my-2">Library</h1>
<section id="searchArea" class="container my-4">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
        <label for="bookSearch" class="mr-2"><strong>Fill in (part of) title:</strong></label>
        <div class="input-group">
            <input type="text" class="form-control" id="bookSearch" name="bookSearchInput"
                   placeholder="type in here..."
                   value="<?php if (isset($_POST['bookSearchInput'])) echo $_POST['bookSearchInput'] ?>">

            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
    <div class="mt-2 d-flex justify-content-between">
        <form method="post" class="" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label for="genre" class=""><strong>Pick Genre:</strong></label>
            <div class="input-group">
                <select name="genre" id="genre">
                    <option value=""><?php if(isset($_POST['genre'])) echo "chose: {$_POST['genre']}"; ?></option>
                    <?php
                    foreach (Genre::getGenres($library) as $genre) {
                        echo " <option value='$genre'>$genre</option>";
                    }
                    ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label for="genre" class=""><strong>Pick Publisher:</strong></label>
            <div class="input-group">
                <select name="publisher" id="publisher">
                    <option value=""><?php if(isset($_POST['publisher'])) echo "chose: {$_POST['publisher']}"; ?></option>
                    <?php
                    foreach (Publisher::getPublishers($library) as $publisher) {
                        echo " <option value='$publisher'>$publisher</option>";
                    }
                    ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>
</section>
<section id="pagesinfo" class="container">
    <?php if ($_SERVER['REQUEST_METHOD']=='POST') echo "<h2 class='my-3'>Total pages for this search: {$searchInput->totalPages($searchInput,$searchMatches)}</h2>"; ?>
</section>
<section id="inventory" class="container text-center">
    <div class="row ml-2">
        <?php if ($_SERVER['REQUEST_METHOD']=='POST') echo $library->displayBooks($searchMatches);?>
    </div>
</section>
</body>
</html>



