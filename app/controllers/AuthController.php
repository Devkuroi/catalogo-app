<?php
/**
 * Autenticación: inicio de sesión, registro y cierre de sesión.
 */
class AuthController extends Controller
{
    public function login(): void
    {
        $this->soloInvitados();

        $modoDemo = (bool) App::config('app')['modo_demo'];
        $usuarios = new Usuario();
        if ($modoDemo) {
            $usuarios->asegurarCuentasDemo(App::config('cuentas_demo'));
        }

        if ($this->esPost()) {
            $this->verificarCsrf('login');

            $correo   = trim($_POST['correo'] ?? '');
            $password = (string) ($_POST['password'] ?? '');

            if ($correo === '' || $password === '') {
                Session::guardarFormulario(['correo' => $correo], ['general' => 'Escribe tu correo y tu contraseña.']);
                $this->redirect('login');
            }

            $usuario = $usuarios->buscarPorCorreo($correo);

            // Mensaje genérico: no revela si el correo existe o no
            if (!$usuario || !password_verify($password, $usuario['password'])) {
                Session::guardarFormulario(['correo' => $correo], ['general' => 'Correo o contraseña incorrectos.']);
                $this->redirect('login');
            }

            Session::login($usuario);
            Session::flash('success', '¡Hola de nuevo, ' . $usuario['nombre'] . '!');
            $this->redirect($usuario['rol'] === 'admin' ? 'admin' : 'catalogo');
        }

        [$old, $errores] = Session::obtenerFormulario();

        $this->render('auth/login', [
            'titulo'       => 'Iniciar sesión',
            'old'          => $old,
            'errores'      => $errores,
            'cuentasDemo'  => $modoDemo ? App::config('cuentas_demo') : [],
        ], 'auth');
    }

    public function registro(): void
    {
        $this->soloInvitados();

        if ($this->esPost()) {
            $this->verificarCsrf('registro');

            $usuarios = new Usuario();
            [$datos, $errores] = $usuarios->validarRegistro(Usuario::limpiar($_POST));

            if ($errores) {
                unset($datos['password'], $datos['confirm_password']); // nunca se devuelven contraseñas
                Session::guardarFormulario($datos, $errores);
                $this->redirect('registro');
            }

            $usuarios->crear($datos);
            Session::guardarFormulario(['correo' => $datos['correo']], []);
            Session::flash('success', '¡Cuenta creada, ' . $datos['nombre'] . '! Ya puedes iniciar sesión.');
            $this->redirect('login');
        }

        [$old, $errores] = Session::obtenerFormulario();

        $this->render('auth/registro', [
            'titulo'   => 'Crear cuenta',
            'old'      => $old,
            'errores'  => $errores,
            'paises'   => Ubicacion::PAISES,
            'ciudades' => Ubicacion::ciudades(),
        ], 'auth');
    }

    public function logout(): void
    {
        if ($this->esPost() && Session::csrfValido($_POST['_csrf'] ?? null)) {
            Session::logout();
            Session::iniciar();
            Session::flash('info', 'Cerraste sesión correctamente.');
        }
        $this->redirect('login');
    }
}
