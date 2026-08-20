<div class="login">
    <div class="login-screen">
		<div class="app-title" style="text-align:center;">
			<img src="/Berdez/fpdf/tutorial/logo.png"
				alt="Constructora Berdez"
				style="width:240px; display:block; margin:0 auto 10px auto;">

			<h1 style="margin-top:0;">Almacén</h1>
		</div>

        <form action="?controller=Login&action=verificar" method="post">
            <div class="login-form">
                <div class="control-group">
                    <input type="text" class="login-field" placeholder="Identificación"
                           id="login-name" name="identificacion" required>
                    <label class="login-field-icon fui-user" for="login-name"></label>
                </div>

                <div class="control-group">
                    <input type="password" class="login-field" placeholder="Contraseña"
                           id="login-pass" name="clave" required>
                    <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>

                <button type="submit" class="btn btn-primary btn-large btn-block" name="btnLogin">
                    <span class="glyphicon glyphicon-log-in"></span> Ingresar
                </button>
            </div>
        </form>
    </div>
</div>