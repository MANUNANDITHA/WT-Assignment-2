package com.portal;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

@WebServlet("/students")
public class StudentServlet extends HttpServlet {

    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        // only logged-in users may continue
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("userId") == null) {
            response.sendRedirect("login.jsp");
            return;
        }

        String sql = "SELECT s.id, s.name, s.roll_number, s.email, s.course, " +
                     "f.file_id, f.doc_type, f.original_name, f.file_type, f.upload_date " +
                     "FROM students s LEFT JOIN student_files f ON f.student_id = s.id " +
                     "ORDER BY s.id, f.file_id";

        // one entry per student, each holding a list of its files
        Map<Integer, Map<String, Object>> students = new LinkedHashMap<>();

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                int id = rs.getInt("id");
                Map<String, Object> student = students.get(id);
                if (student == null) {
                    student = new LinkedHashMap<>();
                    student.put("name", rs.getString("name"));
                    student.put("roll", rs.getString("roll_number"));
                    student.put("email", rs.getString("email"));
                    student.put("course", rs.getString("course"));
                    student.put("files", new ArrayList<Map<String, Object>>());
                    students.put(id, student);
                }
                if (rs.getObject("file_id") != null) {
                    Map<String, Object> file = new LinkedHashMap<>();
                    file.put("id", rs.getInt("file_id"));
                    file.put("docType", rs.getString("doc_type"));
                    file.put("name", rs.getString("original_name"));
                    file.put("type", rs.getString("file_type"));
                    file.put("date", rs.getString("upload_date"));
                    @SuppressWarnings("unchecked")
                    List<Map<String, Object>> files = (List<Map<String, Object>>) student.get("files");
                    files.add(file);
                }
            }
        } catch (Exception e) {
            throw new ServletException("Unable to retrieve student details.", e);
        }

        request.setAttribute("students", new ArrayList<>(students.values()));
        request.getRequestDispatcher("students.jsp").forward(request, response);
    }
}
