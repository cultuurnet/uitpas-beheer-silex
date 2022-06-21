pipeline {
    agent none

    environment {
        PIPELINE_VERSION            = build.pipelineVersion()
        REPOSITORY_NAME             = 'uitpas-balie-api'
    }

    stages {
        stage('Pre build') {
            steps {
                setBuildDisplayName to: env.PIPELINE_VERSION
                sendBuildNotification()
            }
        }

        stage('Setup and build') {
            agent { label 'ubuntu' && '16.04' && 'php7.1' }
            environment {
                GIT_SHORT_COMMIT = build.shortCommitRef()
                ARTIFACT_VERSION = "${env.PIPELINE_VERSION}" + '+sha.' + "${env.GIT_SHORT_COMMIT}"
            }
            stages {
                stage('Setup') {
                    steps {
                        sh label: 'Install rubygems', script: 'bundle install --deployment'
                    }
                }
                stage('Build') {
                    steps {
                        sh label: 'Build binaries', script: 'bundle exec rake uitpas-balie-api:build'
                    }
                }
                stage('Build artifact') {
                    steps {
                        sh label: 'Build artifact', script: "bundle exec rake uitpas-balie-api:build_artifact ARTIFACT_VERSION=${env.ARTIFACT_VERSION}"
                        archiveArtifacts artifacts: "pkg/*${env.ARTIFACT_VERSION}*.deb", onlyIfSuccessful: true
                    }
                }
            }
            post {
                cleanup {
                    cleanWs()
                }
            }
        }

        stage('Upload artifact') {
            agent any
            options { skipDefaultCheckout() }
            steps {
                copyArtifacts filter: 'pkg/*.deb', projectName: env.JOB_NAME, flatten: true, selector: specific(env.BUILD_NUMBER)
                uploadAptlyArtifacts artifacts: '*.deb', repository: env.REPOSITORY_NAME
                createAptlySnapshot name: "${env.REPOSITORY_NAME}-${env.PIPELINE_VERSION}", repository: env.REPOSITORY_NAME
            }
            post {
                cleanup {
                    cleanWs()
                }
            }
        }

        stage('Deploy to development') {
            agent any
            options { skipDefaultCheckout() }
            environment {
                APPLICATION_ENVIRONMENT = 'development'
            }
            steps {
                publishAptlySnapshot snapshotName: "${env.REPOSITORY_NAME}-${env.PIPELINE_VERSION}", publishTarget: "${env.REPOSITORY_NAME}-${env.APPLICATION_ENVIRONMENT}", distributions: 'xenial'
            }
        }

        stage('Deploy to acceptance') {
            agent any
            options { skipDefaultCheckout() }
            environment {
                APPLICATION_ENVIRONMENT = 'acceptance'
            }
            steps {
                publishAptlySnapshot snapshotName: "${env.REPOSITORY_NAME}-${env.PIPELINE_VERSION}", publishTarget: "${env.REPOSITORY_NAME}-${env.APPLICATION_ENVIRONMENT}", distributions: 'xenial'
                triggerDeployment nodeName: 'balie-uitpas-acc02'
            }
            post {
                always {
                    sendBuildNotification to: '#ups-ops', message: "Pipeline <${env.RUN_DISPLAY_URL}|${env.JOB_NAME} [${currentBuild.displayName}]>: deployed to *${env.APPLICATION_ENVIRONMENT}*"
                }
            }
        }

        stage('Deploy to testing') {
            input { message "Deploy to Testing?" }
            agent any
            options { skipDefaultCheckout() }
            environment {
                APPLICATION_ENVIRONMENT = 'testing'
            }
            steps {
                publishAptlySnapshot snapshotName: "${env.REPOSITORY_NAME}-${env.PIPELINE_VERSION}", publishTarget: "${env.REPOSITORY_NAME}-${env.APPLICATION_ENVIRONMENT}", distributions: 'xenial'
                triggerDeployment nodeName: 'balie-uitpas-test02'
            }
            post {
                always {
                    sendBuildNotification to: '#ups-ops', message: "Pipeline <${env.RUN_DISPLAY_URL}|${env.JOB_NAME} [${currentBuild.displayName}]>: deployed to *${env.APPLICATION_ENVIRONMENT}*"
                }
            }
        }

        stage('Deploy to production') {
            input { message "Deploy to Production?" }
            agent any
            options { skipDefaultCheckout() }
            environment {
                APPLICATION_ENVIRONMENT = 'production'
            }
            steps {
                publishAptlySnapshot snapshotName: "${env.REPOSITORY_NAME}-${env.PIPELINE_VERSION}", publishTarget: "${env.REPOSITORY_NAME}-${env.APPLICATION_ENVIRONMENT}", distributions: 'xenial'
                triggerDeployment nodeName: 'balie-uitpas-prod02'
            }
            post {
                always {
                    sendBuildNotification to: '#ups-ops', message: "Pipeline <${env.RUN_DISPLAY_URL}|${env.JOB_NAME} [${currentBuild.displayName}]>: deployed to *${env.APPLICATION_ENVIRONMENT}*"
                }
                cleanup {
                    cleanupAptlySnapshots repository: env.REPOSITORY_NAME
                }
            }
        }

        stage('Tag release') {
            agent { label 'ubuntu' && '16.04' }
            steps {
                copyArtifacts filter: 'pkg/*.deb', projectName: env.JOB_NAME, flatten: true, selector: specific(env.BUILD_NUMBER)
                tagRelease commitHash: artifact.metadata(artifactFilter: '*.deb', field: 'git-ref')
            }
            post {
                cleanup {
                    cleanWs()
                }
            }
        }
    }

    post {
        always {
            sendBuildNotification()
        }
    }
}
