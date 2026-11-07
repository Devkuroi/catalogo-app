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
        PHP_BIN      = 'php'
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

                    // Diagnóstico de versión de PHP en el agente
                    if (isUnix()) {
                        sh 'php -v'
                        sh 'git --version'
                    } else {
                        bat 'php -v'
                        bat 'git --version'
                    }
                }
            }
        }

        stage('Verificación de Sintaxis (Linting)') {
            steps {
                script {
                    echo ">> Validando sintaxis PHP en todos los archivos del proyecto..."
                    if (isUnix()) {
                        sh '''
                            find . -type f -name "*.php" ! -path "*/vendor/*" -exec php -l {} \\;
                        '''
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
                    echo ">> Ejecutando suite de pruebas automatizadas (Validación, Autocompletado, Catálogo)..."
                    if (isUnix()) {
                        sh 'php tests/run_tests.php'
                    } else {
                        bat 'php tests/run_tests.php'
                    }
                }
            }
            post {
                always {
                    // Publicar reporte de pruebas JUnit en la interfaz de Jenkins
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
                        '''
                    } else {
                        powershell '''
                            if (-not (Test-Path "app/.htaccess")) { Write-Error "Falta app/.htaccess"; exit 1 }
                            if (-not (Test-Path "sql/.htaccess")) { Write-Error "Falta sql/.htaccess"; exit 1 }
                            Write-Host "✔ Archivos de seguridad .htaccess verificados correctamente."
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
            echo " Todas las verificaciones, pruebas y empaquetado OK."
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
