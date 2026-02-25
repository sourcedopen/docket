variable "DEFAULT_TAG" {
  default = "docket:local"
}

# Special target consumed by docker/metadata-action
target "docker-metadata-action" {}

target "deploy" {
  inherits   = ["docker-metadata-action"]
  context    = "."
  target     = "deploy"
  platforms  = ["linux/amd64", "linux/arm64"]
  cache-from = ["type=gha"]
  cache-to   = ["type=gha,mode=max"]
}
