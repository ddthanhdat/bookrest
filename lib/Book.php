<?php

class Book
{
	public $id = false;
	public $title;
	public $author;
	public $isbn;

	public static function all()
	{
		$books = [];

		try {
			$dbh = Db::getInstance();
			$stmt = $dbh->prepare("select * from books");
			$stmt->execute();
			while($row = $stmt->fetch()) {
				$book = static::fromArray($row);
				$books[] = $book;
			}
		} catch(Exception $e) {
			throw $e;
		}

		return $books;
	}

	public static function find($id)
	{
		$book = null;

		try {
			$dbh = Db::getInstance();
			$stmt = $dbh->prepare("select * from books where id = :id");
			$stmt->execute(['id' => $id]);
			if ($row = $stmt->fetch()) {
				$book = static::fromArray($row);
			}
		} catch (Exception $e) {
			throw $e;
		}	

		return $book;			
	}

	public static function create(array $data)
	{
		$book = static::fromArray($data);
		$status = $book->save();

		if ($status) {
			return $book;
		}

		return $status;
	}

	protected static function fromArray(array $data)
	{
		$book = new Book();

		$book->id = isset($data["id"]) ? $data["id"] : false;
		$book->title = $data["title"];
		$book->author = $data["author"];
		$book->isbn = $data["isbn"];

		return $book;
	}

	public function delete()
	{
		$status = false;

		try {
			$dbh = Db::getInstance();
			$stmt = $dbh->prepare("delete from books where id = :id");
			$status = $stmt->execute(['id' => $this->id]);
		} catch (Exception $e) {
			throw $e;
		}	

		return $status;
	}

	public function save()
	{
		$status = false;

		try {
			$dbh = Db::getInstance();

			if (($this->id === false) or (static::find($this->id) == null)) {
				$stmt = $dbh->prepare("insert into books (title, author, isbn) 
					values (:title, :author, :isbn)");
				$params = [
					"title" => $this->title,
					"author" => $this->author,
					"isbn" => $this->isbn
				];
				$status = $stmt->execute($params);
				if ($status) {
					$this->id = $dbh->lastInsertId();
				}
			} else {
				$stmt = $dbh->prepare("update books set title = :title, 
					author = :author, isbn = :isbn where id = :id");
				$params = [
					"title" => $this->title,
					"author" => $this->author,
					"isbn" => $this->isbn,
					"id" => $this->id
				];
				$status = $stmt->execute($params);				
			}
		} catch (Exception $e) {
			throw $e;
		}		

		return $status;		
	}
}
