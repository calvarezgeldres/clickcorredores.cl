<%@ WebHandler Language="C#" Class="HelloWorld" %>

using System;
using System.Web;

public class HelloWorld : IHttpHandler
{
    public void ProcessRequest(HttpContext context)
    {
        // Establecer el tipo de contenido de la respuesta
        context.Response.ContentType = "text/plain";
        
        // Enviar la respuesta
        context.Response.Write("Hello, World!");
    }

    public bool IsReusable
    {
        get { return false; }
    }
}