# Serverless API skeleton

## 👩🏻‍💻 Learn more
If you want to learn more about how to use and the assumptions behind this repo, please read this article
https://dev.to/ddesio/superpower-rest-api-dx-with-serverless-and-devops-best-practices-with-aws-51f6

Comments, share, PRs are welcomed.

## 💡 What you will find in this repo
This repo is strongly based on implementing a REST API following [AWS Serverless Multi-Tier Architectures with Amazon API Gateway and AWS Lambda](https://docs.aws.amazon.com/whitepapers/latest/serverless-multi-tier-architectures-api-gateway-lambda/welcome.html) architecture patterns.<br>
This repo contains a serverless REST API which:
- use AWS Lambda as compute layer of serverless tier
- use API Gateway as interface layer of serverless tier
- use AWS CodePipeline and CodeBuild as CI/CD Pipeline to deploy the API

In this repo we adopt those DevOps practices:
- IaC with Serverless Framework and Cloudformation
- [OpenAPI](https://www.openapis.org/) documentation and Doc as Code integrated in IaC with Serverless OpenApi Documenter plugin
- TDD with jest based on OpenAPI Doc
- CI/CD

## ❓Why OpenAPI
<img src="https://www.openapis.org/wp-content/uploads/sites/3/2023/05/What-is-OpenAPI-Simple-API-Lifecycle-Vertical.png">

We suggest you to base your development process following those steps:

- get requirements
- design your API with OpenAPI
- configure infrastructure in IaC (IaC with serverless framework)
- decorate your IaC with Doc As Code using your OpenAPI specification (DaC with serverless openapi documenter)
- write test to ensure OpenAPI is validated and functionality is as expected (TDD with jest)
- write code until all our test are green
- deploy both API and documentation using CI/CD 

## ⚡ [Setup serverless](https://www.serverless.com)
To start working locally and deploy this project you'll need to install and configure serverless following those steps:

- Install [Serverless](https://www.serverless.com/framework/docs/getting-started)

```terminal
npm install -g serverless
```

or

```terminal
cd .dev/docker && docker stop $(docker ps -aq) && docker-compose build && docker-compose up -d
```

## 🐳 Local Environment
This repo comes with a PHP-fpm docker container to execute useful command prepared for you in `cmd` folder
Execute this script to start them.

```bash
npm run docker
```

## 🚀 Run Locally
When your container is active you run a local development server based on Bref [dev-server](https://github.com/brefphp/dev-server)

```terminal
cmd/server
```

or, if you do not use docker and you have PHP installed on your host

```terminal
vendor/bin/bref-server
```

It will start for you a server and run the api at http://0.0.0.0:8000 emulating API Gateway and Lambda locally with Bref
It will use your ```.env.local``` configuration if present, or your ```.env.dev``` configuration as a fallback.

## ⚡ Serverless.yml
Serverless architecture is defined in root file ```serverless.yml```.<br>
This file is made up of very important sections:
- service: name of your deployed service
- frameworkVersion: to define serverless version
- useDotEnv: to load .env files
- provider: global definitions for AWS provider, loaded with a ```config/serverless-provider.yml``` file
- plugins: serverless plugin list which are used by this project, loaded with a ```config/serverless-plugins.yml``` file
- functions: definition for each function, loaded by specific file for each function (```src/**/**/serverless.yml```)
- custom: custom definitions

## 📄 Documentation as code
Generate documentation running in root directory

```bash
npm run doc
```

This scripts uses [serverless-openapi-documenter](https://www.serverless.com/plugins/serverless-openapi-documenter) and [@redocly/cli](https://redocly.com/docs/cli/)
to create doc resources in ```doc``` folder:
- ```doc/openapi.json```: a OpenApiV3 specification of your API in json format
- ```doc/openapi.yaml```: a OpenApiV3 specification of your API in yaml format
- ```doc/postman.json```: a prepared postman collection to consume your API
- ```doc/index.html```: a static doc file which could be deployed to be consulted (we suggest you to deploy to S3+Cloudformation)


## 🧪 Tests
Sample tests are implemented using [PHPUNIT](https://phpunit.de/index.html) and [openapi-psr7-validator](https://github.com/thephpleague/openapi-psr7-validator)
Tests under ```tests``` folder, validate request and response model against generated OpenApi V3 specification, which are defined in your ```severless.yml``` architecture file (importing ```models``` folder files).

Please be sure to generate doc files before testing running
```bash
npm run doc
```

Then copy ```.env.dist``` to ```.env.test```, and customize your env vars.

Finally, run your test with

```bash
cmd/test
```

or, if you do not use docker and you have PHP installed on your host

```bash
vendor/bin/phpunit
```

## 👣 Cloud Footprint
It is a best practice to reduce lambda package footprint (package size) and general cloud footprint (unused resources).<br>
To reduce your lambda footprint:
- package individually each function in your ```serverless.yml``` file as we have done in this repo
```yaml
## Package individually each function
package:
  individually: true
```
- define always ```package``` tag with patterns for include only needed file for each function as we have done in this repo
```yaml
package: #package patterns
    include:
      - "!**/*"
      - vendor/**
      - src/function/hello/**
```

To reduce your cloud footprint, delete unused lambda version and layers which were created deploying your api.
You can use [serverless-prune-plugin](https://github.com/claygregory/serverless-prune-plugin) as we have done in this repo to automatically prune older version as follow:
```yaml
prune: #enable prune
    automatic: true #allow automatic prune
    includeLayers: true #allow layers prune
    number: 3 #retain at lease 3 version

```

## ☁️ Deploy API on AWS Cloud

### Deploy from your local environment

Before proceed:
- Create AWS access key or ask one to your team
- Configure local serverless profiles for dev, staging, prod environments with
```
sls config credentials --provider aws --key <key> --secret <secret> --profile dev
sls config credentials --provider aws --key <key> --secret <secret> --profile staging
sls config credentials --provider aws --key <key> --secret <secret> --profile prod
```
<b>
⚠️ Please store securely your dev, staging, prod keys and secret<br>
⚠️ You should never commit those keys and secret into this repo.<br>
⚠️ You should never set those keys and secret into .env.dist configuration file.️<br>
</b>

Please be sure to update those variables in your ```.env.* files```.
You should have at least three files: ```.env.dev```, ```.env.staging``` and ```.env.prod```.
Those will be used to deploy respectively ``` dev```, ```staging``` and ```prod``` stages.

```dotenv
#APP CONFIG
SERVICE_NAME=my-api
APP_ENV=dev
STAGE_NAME=dev
#AWS CONFIG
AWS_REGION=eu-west-1 ##AWS REGION
SG1=xxx #LAMBDA SECURITY GROUP IN DEV/PROD VPC
SUBNET1=xxx #VPC PRIVATE SUBNET1 IN DEV/PROD VPC
SUBNET2=xxx #VPC PRIVATE SUBNET1 IN DEV/PROD VPC
SUBNET3=xxx #VPC PRIVATE SUBNET1 IN DEV/PROD VPC
#RDS CONFIG
DB_HOST=xxx
DB_DATABASE=xxx
DB_USERNAME=xxx
DB_PASSWORD=xxx
```

Be aware to update RDS Config environment variables depending on the stage (dev/staging/prod).
Be aware to update SG and SUBNETS variables depending on the stage (dev/staging/prod).

Run this choosing a stage (dev/staging/prod) and relative profile (dev/staging/prod) when deploying
```bash
sls deploy --aws-profile $PROFILE --stage $STAGE_NAME 
```

### Deploy with AWS CodePipeline and AWS CodeBuild
You will find a preconfigured ```buildspec.yml``` which install, build, deploy and generate docs on AWS cloud.<br>
You can use it as build specification for AWS CodeBuild project triggered by AWS CodePipeline.<br>
We suggest you to have a specific pipeline per stage dev/staging/v1 connected to specific branches on git (using gitflow).

### Infrastructure Versioning
We ensure a separate Cloud Stack per each stage and version (i.e dev, staging, uat, v1).
A CloudFormation template under ```.dev/cf/api-resources.yaml``` template is useful to create all cloud resources needed:
- one VPC, with 3 public subnet and 3 private subnet (shared between versions)
- one NATGW (AZ-1A) (please change this to three, one per each private to be compliant with HA standards)
- an RDS as database (you can choose to use DynamoDB as well to be full serverless)
- needed security groups to let services be able to connect and communicate
- a Codebuild Project, shared between our pipelines, to build and deploy your doc and solution
- three Pipelines as CI/CD to deploy dev, staging, v1 versions of this API
- one S3 bucket to store documentation versioned under "stage named" prefix
- Coudfront Distribution to expose documentation

You should create parameters ```DB-PASSWORD``` in ParameterStore, and load SSL certificate before launch this template.
