# Pasta de certificado digital

Esta pasta é o local de runtime para o certificado PFX (A1) usado para assinar a NFS-e, e para
os arquivos `.pem` derivados dele (gerados automaticamente na primeira execução e reaproveitados
até vencerem).

Nenhum arquivo `.pfx` ou `.pem` real deve ser commitado aqui — eles estão listados no `.gitignore`
da raiz do projeto. Um certificado real já esteve commitado no histórico deste repositório antes
desta limpeza; ele já está expirado, mas trate qualquer certificado de produção como segredo.

Para desenvolvimento/testes, use um certificado autoassinado de exemplo (ver `tests/fixtures`,
adicionado nas próximas etapas da refatoração).
