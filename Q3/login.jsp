<%@ page contentType="text/html;charset=UTF-8" %>
<!DOCTYPE html>
<html>
<head>
  <title>Student Portal Login</title>
  <style>
    body { font-family: Arial, sans-serif; background: #fdf3e7; margin: 0; }
    .top { background: #e67e22; color: #fff; padding: 16px 30px; font-size: 20px; font-weight: bold; }
    .login-box { max-width: 350px; margin: 50px auto; background: #fff; padding: 25px;
                 border-radius: 10px; box-shadow: 0 2px 8px #0002; }
    h2 { color: #d35400; text-align: center; margin-top: 0; }
    label { font-weight: bold; }
    input, button { width: 100%; box-sizing: border-box; padding: 10px; margin: 6px 0 10px; }
    button { background: #e67e22; color: #fff; border: 0; border-radius: 5px;
             cursor: pointer; font-size: 15px; }
    .error { color: #b42318; background: #fdecea; padding: 8px; border-radius: 5px; }
  </style>
</head>
<body>
<div class="top">Student Portal</div>
<div class="login-box">
  <h2>Login</h2>
  <% if (request.getAttribute("error") != null) { %>
    <p class="error"><%= request.getAttribute("error") %></p>
  <% } %>
  <form action="login" method="post">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>
