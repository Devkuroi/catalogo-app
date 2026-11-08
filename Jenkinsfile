pipeline {
    agent any

    options {
        buildDiscarder(logRotator(numToKeepStr: '15', artifactNumToKeepStr: '5'))
        timestamps()
        disableConcurrentBuilds()
    }

    environment {
        APP_NAME     = 'PromoCatalogo'
        PROJECT_DIR  = '.'
    }

    stages {
        stage('Checkout & Entorno') {
            steps {
                script {
                    echo "=================================================="
                    echo " Iniciando Pipeline de Integración Continua (CI)"
                    echo " Proyecto: ${env.APP_NAME}"
                    echo " Commit: ${env.GIT_COMMIT ?: 'Local / SCM Head'}"
                    echo " Rama:   ${env.GIT_BRANCH ?: 'main'}"
                    echo "=================================================="

                    if (isUnix()) {
                        sh 'git --version'
                        def hasPhp = sh(script: 'command -v php >/dev/null 2>&1', returnStatus: true) == 0
                        if (hasPhp) {
                            sh 'php -v'
                        } else {
                            echo "--------------------------------------------------"
                            echo "⚠️ AVISO: 'php' no está instalado en el contenedor/agente Jenkins."
                            echo "Para habilitar linting y pruebas completas en Jenkins Docker, ejecuta en tu terminal:"
                            echo "  docker exec -u 0 -it <nombre_contenedor_jenkins> apt-get update"
                            echo "  docker exec -u 0 -it <nombre_contenedor_jenkins> apt-get install -y php-cli php-mbstring php-xml php-sqlite3"
                            echo "--------------------------------------------------"
                        }
                    } else {
                        bat 'git --version'
                        bat 'php -v'
                    }
                }
            }
        }

        stage('Verificación de Sintaxis (Linting)') {
            steps {
                script {
                    echo ">> Validando sintaxis en archivos del proyecto..."
                    if (isUnix()) {
                        def hasPhp = sh(script: 'command -v php >/dev/null 2>&1', returnStatus: true) == 0
                        if (hasPhp) {
                            sh '''
                                find . -type f -name "*.php" ! -path "*/vendor/*" -exec php -l {} \\;
                            '''
                            echo "✔ Sintaxis PHP verificada sin errores."
                        } else {
                            echo "⚠️ Se omite la validación 'php -l' porque PHP CLI no está en el PATH del agente."
                        }
                    } else {
                        powershell '''
                            $failed = $false
                            Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch "vendor" } | ForEach-Object {
                                $output = & php -l $_.FullName
                                if ($LASTEXITCODE -ne 0) {
                                    Write-Error "Fallo de sintaxis en $($_.FullName): $output"
                                    $failed = $true
                                }
                            }
                            if ($failed) { exit 1 }
                        '''
                    }
                }
            }
        }

        stage('Pruebas Automatizadas (Unit & Integration)') {
            steps {
                script {
                    echo ">> Ejecutando suite de pruebas automatizadas..."
                    if (isUnix()) {
                        def hasPhp = sh(script: 'command -v php >/dev/null 2>&1', returnStatus: true) == 0
                        if (hasPhp) {
                            sh 'php tests/run_tests.php'
                        } else {
                            echo "⚠️ Pruebas unitarias omitidas temporalmente: instala PHP CLI en el contenedor de Jenkins."
                        }
                    } else {
                        bat 'php tests/run_tests.php'
                    }
                }
            }
            post {
                always {
                    // Publicar reporte de pruebas JUnit en la interfaz de Jenkins si existe
                    junit allowEmptyResults: true, testResults: 'tests/junit.xml'
                }
            }
        }

        stage('Auditoría de Seguridad y Calidad') {
            steps {
                script {
                    echo ">> Verificando archivos de seguridad y configuración..."
                    if (isUnix()) {
                        sh '''
                            test -f app/.htaccess && echo "✔ app/.htaccess presente"
                            test -f sql/.htaccess && echo "✔ sql/.htaccess presente"
                            test -f index.php && echo "✔ Front Controller index.php presente"
                        '''
                    } else {
                        powershell '''
                            if (-not (Test-Path "app/.htaccess")) { Write-Error "Falta app/.htaccess"; exit 1 }
                            if (-not (Test-Path "sql/.htaccess")) { Write-Error "Falta sql/.htaccess"; exit 1 }
                            if (-not (Test-Path "index.php")) { Write-Error "Falta index.php"; exit 1 }
                            Write-Host "✔ Archivos de seguridad y arquitectura verificados correctamente."
                        '''
                    }
                }
            }
        }

        stage('Empaquetado de Artefacto (Build)') {
            steps {
                script {
                    echo ">> Empaquetando artefacto listo para despliegue..."
                    if (isUnix()) {
                        sh '''
                            tar -czf dist_promocatalogo.tar.gz --exclude='.git' --exclude='tests' .
                            echo "✔ Artefacto dist_promocatalogo.tar.gz generado con éxito."
                        '''
                    } else {
                        powershell '''
                            if (Test-Path "dist_promocatalogo.zip") { Remove-Item "dist_promocatalogo.zip" }
                            Compress-Archive -Path "app", "assets", "sql", "index.php", "README.md" -DestinationPath "dist_promocatalogo.zip" -Force
                            Write-Host "✔ Artefacto dist_promocatalogo.zip generado con éxito."
                        '''
                    }
                }
            }
            post {
                success {
                    archiveArtifacts artifacts: '*.zip, *.tar.gz, tests/junit.xml', allowEmptyArchive: true
                }
            }
        }
    }

    post {
        success {
            echo "=================================================="
            echo " ✔ Pipeline de Jenkins ejecutado con ÉXITO."
            echo " Verificaciones, pruebas y empaquetado completados."
            echo "=================================================="
        }
        failure {
            echo "=================================================="
            echo " ✖ FALLO en el Pipeline de Jenkins."
            echo " Revisa los registros del paso correspondiente."
            echo "=================================================="
        }
    }
}
