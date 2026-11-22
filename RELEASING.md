# Releasing

payment-simulator is an application: a release is a git tag plus a GitHub release with the CHANGELOG section as notes. There is nothing to publish on Packagist. Tags have no `v` prefix: `1.0.0`, or `1.1.0-rc1` for a pre-release. A major version means "deployment or configuration changes required" (for example a new minimum PHP version or Docker base image).

## Steps

1. In a pull request, move the `## [Unreleased][unreleased]` entries in `CHANGELOG.md` into a new `## [X.Y.Z] - YYYY-MM-DD` section below it and leave the empty `## [Unreleased][unreleased]` heading in place. At the bottom, change the `[unreleased]` link to `compare/X.Y.Z...HEAD` and add `[X.Y.Z]: https://github.com/tomaj/payment-simulator/compare/<previous>...X.Y.Z` (the first release links to `releases/tag/1.0.0`). Update the supported versions in `SECURITY.md` if needed. Check the section locally; it is the text of the release notes:

   ```bash
   .github/scripts/release-notes.sh X.Y.Z
   ```

2. Merge the pull request and wait for CI on `master`.
3. Tag the merge commit and push the tag:

   ```bash
   git switch master && git pull
   git tag -a X.Y.Z -m X.Y.Z
   git push origin X.Y.Z
   ```

4. Deploy the tag, close the `X.Y.Z` milestone and open the next one.

The `release.yml` workflow then fails before publishing anything when `CHANGELOG.md` has no `## [X.Y.Z] - <date>` section; otherwise it builds `payment-simulator-X.Y.Z.tar.gz` and `.zip` with `git archive` plus `SHA256SUMS`, attests their provenance, and creates the GitHub release (a pre-release when the tag has a `-` suffix).

## When something goes wrong

A failed run publishes nothing. Fix `CHANGELOG.md` on `master`, then move the tag and push it again:

```bash
git push origin :refs/tags/X.Y.Z
git tag -f -a X.Y.Z -m X.Y.Z
git push origin X.Y.Z
```

Do not move a tag after the release was published; release a new patch version instead.
