<%@ page contentType="text/html;charset=UTF-8" import="java.util.*" %>
<%!
  // escape special HTML characters before printing database text
  public String esc(Object value) {
      if (value == null) return "";
      return value.toString().replace("&", "&amp;").replace("<", "&lt;")
              .replace(">", "&gt;").replace("\"", "&quot;").replace("'", "&#39;");
  }
%>
<%
  // second check: this page is for logged-in users only
  if (session.getAttribute("userId") == null) {
      response.sendRedirect("login.jsp");
      return;
  }
  List<Map<String, Object>> students = (List<Map<String, Object>>) request.getAttribute("students");
  if (students == null) {            // page opened directly, go through the servlet
      response.sendRedirect("students");
      return;
  }
%>
<!DOCTYPE html>
<html>
<head>
  <title>Student Profiles</title>
  <style>
    body { font-family: Arial, sans-serif; background: #fdf3e7; margin: 0; }
    .top { background: #e67e22; color: #fff; padding: 14px 30px; display: flex;
           justify-content: space-between; align-items: center; font-size: 20px; font-weight: bold; }
    .top form { margin: 0; }
    .top button { background: #fff; color: #d35400; border: 0; padding: 8px 14px;
                  border-radius: 5px; cursor: pointer; font-weight: bold; }
    .profile { max-width: 760px; margin: 20px auto; background: #fff; padding: 20px;
               border-radius: 10px; box-shadow: 0 2px 8px #0002; }
    h3 { color: #d35400; margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 8px; border: 1px solid #eee; text-align: left; vertical-align: middle; }
    th { background: #fdebd8; }
    img { max-width: 120px; height: auto; border-radius: 6px; }
    a { color: #d35400; }
  </style>
</head>
<body>
<div class="top">
  <span>Student Profiles</span>
  <form action="logout" method="post">
    <span style="font-weight:normal;font-size:15px;margin-right:12px;">
      Welcome, <%= esc(session.getAttribute("fullName")) %></span>
    <button type="submit">Logout</button>
  </form>
</div>

<% if (students.isEmpty()) { %>
  <div class="profile"><p>No student records found.</p></div>
<% } else { %>
  <% for (Map<String, Object> student : students) { %>
    <div class="profile">
      <h3><%= esc(student.get("name")) %></h3>
      <p><b>Roll Number:</b> <%= esc(student.get("roll")) %> &nbsp;|&nbsp;
         <b>Email:</b> <%= esc(student.get("email")) %> &nbsp;|&nbsp;
         <b>Course:</b> <%= esc(student.get("course")) %></p>

      <% List<Map<String, Object>> files = (List<Map<String, Object>>) student.get("files"); %>
      <% if (files.isEmpty()) { %>
        <p>No uploaded files.</p>
      <% } else { %>
        <table>
          <tr><th>Document</th><th>File name</th><th>Uploaded on</th><th>Preview</th></tr>
          <% for (Map<String, Object> f : files) { %>
            <tr>
              <td><%= esc(f.get("docType")) %></td>
              <td><%= esc(f.get("name")) %></td>
              <td><%= esc(f.get("date")) %></td>
              <td>
                <% if (String.valueOf(f.get("type")).startsWith("image/")) { %>
                  <img src="file?id=<%= f.get("id") %>" alt="<%= esc(f.get("docType")) %>">
                <% } else { %>
                  <a href="file?id=<%= f.get("id") %>" target="_blank">Open PDF</a>
                <% } %>
              </td>
            </tr>
          <% } %>
        </table>
      <% } %>
    </div>
  <% } %>
<% } %>
</body>
</html>
