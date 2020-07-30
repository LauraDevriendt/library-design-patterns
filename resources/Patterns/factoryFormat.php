<?php
abstract class BookImporter{
    const FORMAT="JSON"; // @TODO NODIG???
    abstract public function convertData():array;
    public function getData(): array
    {
        // Call the factory method to create a Product object...
        return $this->convertData();


    }


}

class BookImporterCsv extends BookImporter {
    private string $path;
    private array $books=[];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function convertData():array {
        $bookData = [];
        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if(!isset($first)) {
                    $first = false;
                    continue;
                }

                [$title, $author, $genre, $pages, $publisher] = $data;
                $bookData[] = [
                    'title' => $title,
                    'author' => $author,
                    'genre' => $genre,
                    'pages' => $pages,
                    'publisher' => $publisher,
                ];
            }
            fclose($handle);
            foreach ($bookData as $book){
                $bookForLibrary = new Book($book['title'],$book['author'],$book['genre'],(int) $book['pages'], $book['publisher'], new OpenState());
                $this->books[]=$bookForLibrary;

            }
            return $this->books;

        }
    }


}

class BookImporterJson extends BookImporter {
    private string $path;
    private array $books=[];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function convertData():array {
        $bookData= json_decode(file_get_contents($this->path, true), true, 512, JSON_THROW_ON_ERROR);
        foreach ($bookData as $book){
            $bookForLibrary = new Book($book['title'],$book['author'],$book['genre'],(int) $book['pages'], $book['publisher'], new OpenState());
            $this->books[]=$bookForLibrary;

        }

        return $this->books;
    }


}

function clientCode(BookImporter $creator):array
{

    return $creator->getData();

}