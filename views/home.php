<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Book RESTful APIs</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<style type="text/css" media="screen">
		.custom {
			width: 78px !important;
		}
		h1 {
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="container">
	    <div class="row">

            <div class="page-header">
				<h1>Book RESTful APIs</h1>
			</div>
			
			<div class="col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading text-uppercase">HMAC</div>
                    <div class="panel-body">
						<form id="keys" role="form">
							<div class="form-group">
								<label class="control-label">Public hash:</label>
								<input class="form-control" type="text" 
											name="publicKey" value="" />
							</div>
							<div class="form-group">
								<label class="control-label">Private hash:</label>
								<input class="form-control" type="text" 
											name="privateKey" value="" />
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="panel panel-info">
                    <div class="panel-heading text-uppercase">Thông tin sách</div>
                    <div class="panel-body">
						<form id="book" role="form">
							<div class="form-group">
								<label class="control-label">Mã sách:</label>
								<input class="form-control" type="text" name="id" />
							</div>
							<div class="form-group">
								<label class="control-label">Tựa sách:</label>
								<input class="form-control" type="text" name="title" />
							</div>
							<div class="form-group">
								<label class="control-label">Tác giả:</label>
								<input class="form-control" type="text" name="author" />
							</div>
							<div class="form-group">
								<label class="control-label">Số ISBN:</label>
								<input class="form-control" type="text" name="isbn" />
							</div>	

							<input class="btn btn-info custom" type="submit" 
										name="get" value="GET" />
							<input class="btn btn-primary custom" type="submit"	
										name="post" value="POST" />
							<input class="btn btn-success custom" type="submit" 
										name="put" value="PUT" />
							<input class="btn btn-danger custom" type="submit" 
										name="delete" value="DELETE" />
						</form>
					</div>
				</div>
			</div>

		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/hmac-sha256.js"></script>
	<script>

	// Convert form inputs to object
	$.fn.serializeObject = function() {
	    var o = {};
	    var a = this.serializeArray();
	    $.each(a, function() {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	};

	$(document).ready(function() {

		$("#book").submit(function(event) {
			event.preventDefault();

			var method = $("input[type=submit][clicked=true]").val();
			var bookId = $("input[name=id]").val();
			var body = JSON.stringify($("#book").serializeObject());

			var title = $("input[name=title]");
			var author = $("input[name=author]");
			var isbn = $("input[name=isbn]");
			//alert("HTTP Method: " + method);
			// Tao loi goi ajax den server dua tren method, bookId va body
			
			if(method=="GET"){
				$.ajax({
					url: '/api/v1/books/'+bookId,
					type: "GET",
					dataType: "json",
					//data: {id: bookId},
				})
				.done(function(rs) {
					alert('Thành công');
					$(title).val(rs.book.title);
					$(author).val(rs.book.author);
					$(isbn).val(rs.book.isbn);
					console.log("success");
				})
				.fail(function() {
					alert('Thất bại');
					$(title).val("");
					$(author).val("");
					$(isbn).val("");
				});
				
			}
			if(method=="POST"){
				$.ajax({
					url: '/api/v1/books',
					type: "POST",
					dataType: "json",
					data: {
						id: bookId,
						title : $(title).val(),
						author : $(author).val(),
						isbn : $(isbn).val(),
						},
				})
				.done(function(rs) {
					alert('Thành công');
					$("input[name=id]").val(rs.book.id);
				})
				.fail(function() {
					alert('Thất bại');
					$(title).val("");
					$(author).val("");
					$(isbn).val("");
				});
			}
			if(method=="PUT"){
				$.ajax({
					url: '/api/v1/books/'+bookId,
					type: "PUT",
					dataType: "json",
					data: {
						id: bookId,
						title : $(title).val(),
						author : $(author).val(),
						isbn : $(isbn).val(),
						},
				})
				.done(function(rs) {
					alert('Thành công!');
					$("input[name=id]").val(rs.book.id);
				})
				.fail(function(rs) {
					alert(rs.responseJSON.message);
				});
			}
			if(method=="DELETE"){
				$.ajax({
					url: '/api/v1/books/'+bookId,
					type: "DELETE",
					dataType: "json",
					data: {
						id: bookId
						},
					error: function(rs, status, error) {
						alert(rs.responseJSON.message);
					}
				})
				.done(function(rs) {
					alert('Thành công!');
					$("input[name=id]").val("");
					$(title).val("");
					$(author).val("");
					$(isbn).val("");
				});
			}
		});

		$("form input[type=submit]").click(function() {
		    $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
		    $(this).attr("clicked", "true");
		});
	});
	</script>
</body>
</html>
