import java.io.*;
import java.net.*;
import com.sun.net.httpserver.*;
import java.util.regex.*;

import javax.xml.transform.TransformerFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.stream.StreamSource;
import javax.xml.transform.stream.StreamResult;

public class XalanProxy {
    public static void main(String[] args) throws Exception {
        HttpServer server = HttpServer.create(new InetSocketAddress(80), 0);
        server.createContext("/", new HttpHandler() {
            public void handle(HttpExchange exchange) throws IOException {
                String method = exchange.getRequestMethod();
                String targetUrl = "http://127.0.0.1:8080" + exchange.getRequestURI().toString();
                System.out.println("Redirigiendo a: " + targetUrl);
                
                try {
                    URL url = new URL(targetUrl);
                    HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                    conn.setRequestMethod(method);
                    
                    if (method.equalsIgnoreCase("POST") || method.equalsIgnoreCase("PUT")) {
                        conn.setDoOutput(true);
                        try (InputStream reqBody = exchange.getRequestBody();
                             OutputStream os = conn.getOutputStream()) {
                            byte[] buffer = new byte[4096];
                            int bytesRead;
                            while ((bytesRead = reqBody.read(buffer)) != -1) {
                                os.write(buffer, 0, bytesRead);
                            }
                            os.flush();
                        }
                    }
                    
                    InputStream is = conn.getInputStream();
                    String apacheResponse = readStream(is);
                    
                    String processedResponse = processESI(apacheResponse);
                    
                    byte[] responseBytes = processedResponse.getBytes();
                    exchange.getResponseHeaders().add("X-ESI-Enabled", "yes");
                    exchange.sendResponseHeaders(conn.getResponseCode(), responseBytes.length);
                    try (OutputStream os = exchange.getResponseBody()) {
                        os.write(responseBytes);
                    }
                } catch (Exception e) {
                    String errorMsg = "Error en proxy: " + e.getMessage();
                    exchange.sendResponseHeaders(500, errorMsg.getBytes().length);
                    try (OutputStream os = exchange.getResponseBody()) {
                        os.write(errorMsg.getBytes());
                    }
                }
            }
        });
        server.setExecutor(null);
        System.out.println("Proxy vulnerable iniciado en el puerto 80...");
        server.start();
    }
    
    private static String readStream(InputStream is) throws IOException {
        BufferedReader in = new BufferedReader(new InputStreamReader(is));
        StringBuilder sb = new StringBuilder();
        String line;
        while((line = in.readLine()) != null){
            sb.append(line).append("\n");
        }
        return sb.toString();
    }
    
    
    private static String processESI(String content) {
        Pattern pattern = Pattern.compile(
            "<esi:include\\s+src=\"([^\"]+)\"(?:\\s+stylesheet=\"([^\"]+)\")?\\s*/?>",
            Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(content);
        StringBuffer sb = new StringBuffer();
        while (matcher.find()) {
            String src = matcher.group(1);
            String stylesheet = matcher.group(2);
            String replacement = "";
            if (stylesheet != null) {
                replacement = processWithStylesheet(src, stylesheet);
            } else {
                try {
                    replacement = readResource(src);
                } catch(Exception e) {
                    replacement = "Error leyendo recurso: " + e.getMessage();
                }
            }
            matcher.appendReplacement(sb, Matcher.quoteReplacement(replacement));
        }
        matcher.appendTail(sb);
        return sb.toString();
    }

    private static String processWithStylesheet(String xmlSrc, String stylesheetSrc) {
        try {
            TransformerFactory factory = TransformerFactory.newInstance();
            StreamSource xsltSource;
            if (stylesheetSrc.startsWith("http://") || stylesheetSrc.startsWith("https://")) {
                xsltSource = new StreamSource(new URL(stylesheetSrc).openStream());
            } else {
                xsltSource = new StreamSource(new File(stylesheetSrc));
            }
            Transformer transformer = factory.newTransformer(xsltSource);
            StreamSource xmlSource;
            if (xmlSrc.startsWith("http://") || xmlSrc.startsWith("https://")) {
                xmlSource = new StreamSource(new URL(xmlSrc).openStream());
            } else {
                xmlSource = new StreamSource(new File(xmlSrc));
            }
            StringWriter writer = new StringWriter();
            transformer.transform(xmlSource, new StreamResult(writer));
            return writer.toString();
        } catch (Exception e) {
            return "Error procesando con stylesheet: " + e.getMessage();
        }
    }
    
    private static String readResource(String src) throws IOException {
        if (src.startsWith("http://") || src.startsWith("https://")) {
            InputStream in = new URL(src).openStream();
            return readStream(in);
        } else {
            File file = new File(src);
            if (!file.exists()) {
                throw new FileNotFoundException("Recurso no encontrado: " + src);
            }
            return readStream(new FileInputStream(file));
        }
    }
}