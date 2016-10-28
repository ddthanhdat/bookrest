<?php

require_once("vendor/autoload.php");

$app = new \Slim\App();

$app->get("/", function($request, $response) {
	$view = View::make("views/home.php");
	return $response->write($view);
});

$app->get("/api/v1/books", "getBooks");
$app->get("/api/v1/books/{id:[0-9]+}", "getBook");
$app->post("/api/v1/books", "createBook");
$app->put("/api/v1/books/{id:[0-9]+}", "updateBook");
$app->delete("/api/v1/books/{id:[0-9]+}", "deleteBook");

function getBooks($request, $response) {
	// Lấy về toàn bộ sách trong thư viện
}

function getBook($request, $response, $args) {
	// Truy vấn thông tin sách qua id ($args["id"])
	$book = Book::find($args["id"]);

	if ($book != null) {
		$result = ["status" => "success", "book" => $book];
		$status = 200;
	} else {
		// Không tìm thấy sách -> trả về thông báo lỗi
		$result = ["status" => "error", "message" => "Không tìm thấy sách!"];
		$status = 400;
	}

	return $response->withJson($result, $status); 
}

function createBook($request, $response) {
	// Tạo thông tin một sách mới
	// Gửi về cho người dùng thông tin sách mới tạo

	$book = new Book();
	$book->title = $request->getParam("title", $book->title);
	$book->author = $request->getParam("author", $book->author);
	$book->isbn = $request->getParam("isbn", $book->isbn);
	$book->save();

	$result = ["status" => "success", "book" => $book];
	$status = 200;

	return $response->withJson($result, $status); 
}

function updateBook($request, $response, $args) {
	// Tìm kiếm sách dựa trên id gửi từ client
	$book = Book::find($args["id"]);

	// Nếu tìm thấy thì cập nhật nội dung
	// với các dữ liệu gửi đến từ client
	if ($book != null) {
		$book->title = $request->getParam("title", $book->title);
		$book->author = $request->getParam("author", $book->author);
		$book->isbn = $request->getParam("isbn", $book->isbn);
		
		$book->save();

		$result = ["status" => "success", "book" => $book];
		$status = 200;
	} else {
		// Không tìm thấy sách -> trả về thông báo lỗi
		$result = ["status" => "error", "message" => "Không tìm thấy sách!"];
		$status = 400;
	}

	return $response->withJson($result, $status); 
}

function deleteBook($request, $response, $args) {
	// Xóa thông tin một sách dựa trên id ($args["id"])
	// Tìm kiếm sách dựa trên id gửi từ client
	$book = Book::find($args["id"]);
	$result = [];
	if ($book != null) {
		$book->delete();

		$result = ["status" => "success", "book" => $book];
		$status = 200;
	} else {
		// Không tìm thấy sách -> trả về thông báo lỗi
		$result = ["status" => "error", "message" => "Không tìm thấy sách!"];
		$status = 400;
	}

	return $response->withJson($result, $status); 
}

$app->run();
