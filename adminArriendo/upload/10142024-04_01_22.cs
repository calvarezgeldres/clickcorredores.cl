<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="TuNombreDeEspacio.Default" %>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Hola Mundo en ASP.NET</title>
    <meta charset="utf-8" />
</head>
<body>
    <form id="form1" runat="server">
        <div>
            <h1><asp:Label ID="Label1" runat="server" Text="¡Hola, Mundo!"></asp:Label></h1>
            <asp:Button ID="Button1" runat="server" Text="Haz clic aquí" OnClick="Button1_Click" />
        </div>
    </form>
</body>
</html>