#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
REGISTRY="ghcr.io"
IMAGE_NAME="isama92/isalendar"
PLATFORMS="linux/amd64"

echo -e "${GREEN}Isalendar Docker Build & Push Script${NC}"
echo "======================================="
echo ""

# Check if git is available
if ! command -v git &> /dev/null; then
    echo -e "${RED}Error: git is not installed${NC}"
    exit 1
fi

# Check if docker is available
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: docker is not installed${NC}"
    exit 1
fi

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}Error: Not in a git repository${NC}"
    exit 1
fi

# Get current git tag
VERSION=$(git describe --tags --exact-match 2>/dev/null || echo "")

if [ -z "$VERSION" ]; then
    echo -e "${RED}Error: No git tag found on current commit${NC}"
    echo ""
    echo -e "${YELLOW}Would you like to create a new tag?${NC}"
    read -p "Enter version (e.g., v1.0.0) or press Enter to cancel: " NEW_VERSION

    if [ -z "$NEW_VERSION" ]; then
        echo -e "${RED}Cancelled${NC}"
        exit 1
    fi

    # Create the tag
    echo -e "${GREEN}Creating tag ${NEW_VERSION}...${NC}"
    git tag "$NEW_VERSION"

    # Ask if they want to push the tag
    read -p "Push tag to remote? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git push origin "$NEW_VERSION"
        echo -e "${GREEN}Tag pushed to remote${NC}"
    fi

    VERSION="$NEW_VERSION"
fi

echo -e "${GREEN}Found version tag: ${VERSION}${NC}"
echo ""

# Check if working directory is clean
if [ -n "$(git status --porcelain)" ]; then
    echo -e "${YELLOW}Warning: Working directory has uncommitted changes${NC}"
    read -p "Do you want to continue? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check Docker login to GitHub Container Registry
echo -e "${GREEN}Checking Docker authentication...${NC}"
if ! docker info | grep -q "Username"; then
    echo -e "${YELLOW}Not logged in to Docker registry${NC}"
fi

# Attempt to verify ghcr.io authentication
if ! echo "" | docker login "${REGISTRY}" --username isama92 --password-stdin 2>/dev/null; then
    echo -e "${YELLOW}Please log in to GitHub Container Registry${NC}"
    echo ""
    echo "You need a GitHub Personal Access Token with 'write:packages' permission."
    echo "Create one at: https://github.com/settings/tokens"
    echo ""
    read -sp "Enter your GitHub Personal Access Token: " GITHUB_TOKEN
    echo ""

    if [ -z "$GITHUB_TOKEN" ]; then
        echo -e "${RED}No token provided. Exiting.${NC}"
        exit 1
    fi

    echo "$GITHUB_TOKEN" | docker login "${REGISTRY}" --username isama92 --password-stdin

    if [ $? -ne 0 ]; then
        echo -e "${RED}Login failed. Please check your token and try again.${NC}"
        exit 1
    fi

    echo -e "${GREEN}Successfully logged in to ${REGISTRY}${NC}"
fi
echo ""

# Build the image
echo -e "${GREEN}Building Docker image...${NC}"
echo "Platform: ${PLATFORMS}"
echo "Image: ${REGISTRY}/${IMAGE_NAME}:${VERSION}"
echo ""

# Enable BuildKit
export DOCKER_BUILDKIT=1

# Create builder if not exists
if ! docker buildx inspect isalendar-builder > /dev/null 2>&1; then
    echo "Creating buildx builder..."
    docker buildx create --name isalendar-builder --use
else
    docker buildx use isalendar-builder
fi

# Build and push
docker buildx build \
    --platform "${PLATFORMS}" \
    --tag "${REGISTRY}/${IMAGE_NAME}:${VERSION}" \
    --tag "${REGISTRY}/${IMAGE_NAME}:latest" \
    --push \
    .

echo ""
echo -e "${GREEN}✓ Build successful!${NC}"
echo ""
echo "Images pushed:"
echo "  - ${REGISTRY}/${IMAGE_NAME}:${VERSION}"
echo "  - ${REGISTRY}/${IMAGE_NAME}:latest"
echo ""
echo -e "${GREEN}Done!${NC}"
