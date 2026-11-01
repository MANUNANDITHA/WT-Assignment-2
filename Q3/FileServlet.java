package com.portal;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

@WebServlet("/file")
public class FileServlet extends HttpServlet {

    // folder of the PHP admin panel (Question 2); stored paths look like uploads/abc.jpg
    private static final Path BASE_DIR =
        Paths.get("C:/xampp/htdocs/student_admin").toAbsolutePath().normalize();

    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("userId") == null) {
            response.sendError(HttpServletResponse.SC_UNAUTHORIZED);
            return;
        }

        int fileId;
        try {
            fileId = Integer.parseInt(request.getParameter("id"));
        } catch (NumberFormatException e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        String filePathInDb;
        String fileType;
        String sql = "SELECT file_path, file_type FROM student_files WHERE file_id = ?";
        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, fileId);
            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) {
                    response.sendError(HttpServletResponse.SC_NOT_FOUND);
                    return;
                }
                filePathInDb = rs.getString("file_path");
                fileType = rs.getString("file_type");
            }
        } catch (Exception e) {
            throw new ServletException("Unable to read file details.", e);
        }

        // block path traversal: the file must stay inside the base folder
        Path file = BASE_DIR.resolve(filePathInDb).normalize();
        if (!file.startsWith(BASE_DIR) || !Files.isRegularFile(file)) {
            response.sendError(HttpServletResponse.SC_NOT_FOUND);
            return;
        }

        response.setContentType(fileType);
        response.setContentLengthLong(Files.size(file));
        response.setHeader("Content-Disposition", "inline");
        try (InputStream in = Files.newInputStream(file);
             OutputStream out = response.getOutputStream()) {
            byte[] buffer = new byte[4096];
            int count;
            while ((count = in.read(buffer)) != -1) {
                out.write(buffer, 0, count);
            }
        }
    }
}
